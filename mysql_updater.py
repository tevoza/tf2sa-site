import json, requests, mysql.connector
from steam.steamid import SteamID
from dotenv import dotenv_values
#UPDATE GAME STATS FROM LOGSTF

CLASS_ID = {
    "special"       : 0, #Tracks overall stats that can't be attributed to one class
    "scout"         : 1,
    "soldier"       : 2,
    "pyro"          : 3,
    "demoman"       : 4,
    "heavyweapons"  : 5,
    "engineer"      : 6,
    "medic"         : 7,
    "sniper"        : 8,
    "spy"           : 9
}

SPECIAL_CLASS = {2,4,8,9}
DMG_THRESH = 17500 #ignore games where damage above this was obtained.

def DBInit(cursor):
    print("confirming/recreating tables... ", end='')

    #verify
    cursor.execute("""
    CREATE TABLE IF NOT EXISTS Users(
        UserID INT UNSIGNED NOT NULL AUTO_INCREMENT,
        SteamID BIGINT UNSIGNED,
        UserName VARCHAR(32) NOT NULL,
        PassHash CHAR(64) NOT NULL,
        SessionID CHAR(64),
        JoinDate INT UNSIGNED,
        PRIMARY KEY(UserID, UserName)
    ) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;
    """)

    cursor.execute("""
    CREATE TABLE IF NOT EXISTS Games (
        GameID INT UNSIGNED NOT NULL,
        Date INT UNSIGNED,
        Duration SMALLINT,
        BluScore TINYINT UNSIGNED,
        RedScore TINYINT UNSIGNED,
        PRIMARY KEY (GameID)
    ) ENGINE = InnoDB;
    """)

    cursor.execute("""
    CREATE TABLE IF NOT EXISTS Players (
        SteamID BIGINT UNSIGNED NOT NULL,
        PlayerName VARCHAR(32),
        PRIMARY KEY (SteamID)
    ) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;
    """)

    cursor.execute("""
    CREATE TABLE IF NOT EXISTS PlayerStats (
        PlayerStatsID INT UNSIGNED NOT NULL AUTO_INCREMENT,
        GameID INT UNSIGNED NOT NULL,
        SteamID BIGINT UNSIGNED NOT NULL,
        ClassID TINYINT UNSIGNED NOT NULL,
        Playtime SMALLINT UNSIGNED,
        Kills TINYINT UNSIGNED,
        Assists TINYINT UNSIGNED,
        Deaths TINYINT UNSIGNED,
        Damage MEDIUMINT UNSIGNED,
        Airshots TINYINT UNSIGNED,
        Headshots TINYINT UNSIGNED,
        Backstabs TINYINT UNSIGNED,
        Drops MEDIUMINT UNSIGNED,
        Heals MEDIUMINT UNSIGNED,
        Ubers TINYINT UNSIGNED,
        PRIMARY KEY (PlayerStatsID),
        CONSTRAINT `fk_game_id`
            FOREIGN KEY (GameID) REFERENCES Games (GameID)
            ON DELETE CASCADE
            ON UPDATE RESTRICT,
        CONSTRAINT `fk_player_id`
            FOREIGN KEY (SteamID) REFERENCES Players (SteamID)
            ON DELETE CASCADE
            ON UPDATE RESTRICT
    ) ENGINE = InnoDB;
    """)

    cursor.execute("""
    CREATE TABLE IF NOT EXISTS Weapons (
        WeaponID SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
        WeaponName VARCHAR(32),
        PRIMARY KEY (WeaponID)
    ) ENGINE = InnoDB;
    """)

    cursor.execute("""
    CREATE TABLE IF NOT EXISTS WeaponStats (
        PlayerStatsID INT UNSIGNED NOT NULL,
        WeaponID SMALLINT UNSIGNED NOT NULL,
        Accuracy DOUBLE,
        PRIMARY KEY (PlayerStatsID, WeaponID),
        CONSTRAINT `fk_player_stats`
            FOREIGN KEY (PlayerStatsID) REFERENCES PlayerStats (PlayerStatsID)
            ON DELETE CASCADE
            ON UPDATE RESTRICT,
        CONSTRAINT `fk_weapon_id`
            FOREIGN KEY (WeaponID) REFERENCES Weapons (WeaponID)
            ON DELETE CASCADE
            ON UPDATE RESTRICT
    ) ENGINE = InnoDB;
    """)

    cursor.execute("""
    CREATE TABLE IF NOT EXISTS BlacklistGames (
        GameID INT UNSIGNED NOT NULL,
        Reason VARCHAR(32),
        PRIMARY KEY (GameID)
    ) ENGINE = InnoDB;
    """)

        cursor.execute("""
    CREATE TABLE IF NOT EXISTS Comments (
        CommentID INT UNSIGNED NOT NULL AUTO_INCREMENT,
        ThreadID INT UNSIGNED NOT NULL,
        Content VARCHAR(65000),
        Date UNSIGNED INT
        PRIMARY KEY (ThreadIdD, CommentID)
        CONSTRAINT fk_thread_id
            FOREIGN KEY (ThreadID) REFERENCES THREADS (ThreadID)
            ON DELETE CASCADE
            ON UPDATE RESTRICT       
    ) ENGINE = InnoDB;
    """)
    print("done")

def UpdatePlayerNames(cursor):
    Steam_API_Key = env["STEAM_API_KEY"]
    cursor.execute("SELECT SteamID from Players")
    SteamIDTuple = cursor.fetchall()
    IDList = []

    print("Updating player steam usernames.")

    for ID in range(0, len(SteamIDTuple)):
        IDList.append(SteamIDTuple[ID][0])

        if len(IDList) == 100 or ID == len(SteamIDTuple):
            PlayerInfo = json.loads(requests.get("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={}&steamids={}".format(Steam_API_Key, IDList)).text)

            UpdatedPlayerNames = {}

            for Player in PlayerInfo["response"]["players"]:
                UpdatedPlayerNames[Player["steamid"]] = Player["personaname"]

            for SteamID, PlayerName in UpdatedPlayerNames.items():
                qry = "UPDATE Players SET PlayerName = %s WHERE SteamID = %s"
                cursor.execute(qry,(PlayerName, SteamID))

            IDList = []
            UpdatedPlayerNames = {}

    print("done")

def BlackListLog(LogID, reason, cursor):
    cursor.execute("INSERT INTO BlacklistGames (GameID, Reason) VALUES({},'{}')".format(LogID, reason))

#Cheque if valid for further processing. ie
#Cheque if already recorded
#Cheque if valid
def isValidLog(LogID, Log, cursor):
    if (Log["length"] / 60) < 15:
        print("Match " + str(LogID) + " not long enough to be recorded. ", end='')
        BlackListLog(LogID,"Short length", cursor)
        return False

    return True

#Add Player if not added.
#Uses steamid64
def AddPlayer(steamid, name, cursor):
    cursor.execute("Select SteamID from Players where SteamID = {}".format(steamid))
    PlayerExists = cursor.fetchone()
    if PlayerExists:
        return
    else:
        qry = "INSERT INTO Players VALUES (%s, %s)"
        cursor.execute(qry,(steamid, name))

def AddGame(LogID, Log, cursor):
    if isValidLog(LogID, Log, cursor) == False:
        print("Log not valid")
        return

    #Add Game Record
    cursor.execute("""
    INSERT INTO Games
    VALUES ('{}',{},{},{},{})
    """.format(LogID, Log["info"]["date"], Log["info"]["total_length"], Log["teams"]["Blue"]["score"], Log["teams"]["Red"]["score"]))

    #Update Players
    for PlayerID, Name in Log["names"].items():
        AddPlayer(SteamID(PlayerID).as_64, Name, cursor)

    global CLASS_ID
    global SPECIAL_CLASS
    global DMG_THRESH
    for Player,PlayerStats in Log["players"].items():
        SpecialClass = False
        PlayerID = SteamID(Player).as_64
        Damage = PlayerStats["dmg"]
        if Damage > DMG_THRESH:
            cursor.execute("DELETE FROM PlayerStats WHERE GameID={}".format(LogID))
            print("damage threshold exceeded. disregarding game.")
            BlackListLog(LogID,"Damage threshold", cursor)
            break;

        for Class_stats in PlayerStats["class_stats"]:
            if Class_stats["type"] not in CLASS_ID:
                break;
            Class = CLASS_ID[Class_stats["type"]]
            Duration = Class_stats["total_time"]
            if Duration < 0:
                break;
            Kills = Class_stats["kills"]
            Assists = Class_stats["assists"]
            Deaths = Class_stats["deaths"]
            Damage = Class_stats["dmg"]

            if Class == 7:
                Heals = PlayerStats["heal"]
                Ubers = PlayerStats["ubers"]
                Drops = PlayerStats["drops"]
                cursor.execute(''' INSERT INTO PlayerStats
                (GameID, SteamID, ClassID, Playtime, Kills, Assists, Deaths, Damage, Drops, Heals, Ubers) VALUES
                ({},{},{},{},{},{},{},{},{},{},{})
                '''.format(LogID, PlayerID, Class, Duration, Kills, Assists, Deaths, Damage, Drops, Heals, Ubers))
            else:
                cursor.execute(''' INSERT INTO PlayerStats
                (GameID, SteamID, ClassID, Playtime, Kills, Assists, Deaths, Damage) VALUES
                ({},{},{},{},{},{},{},{})
                '''.format(LogID, PlayerID, Class, Duration, Kills, Assists, Deaths, Damage))


            if Class in SPECIAL_CLASS:
                SpecialClass = True

            #TDOD: Add weapon stats
            #for Weapon, WeaponStats in Class_stats["weapon"].items():
            #    P_Weapon_Type = Weapon
            #    if WeaponStats["shots"] != 0:
            #        P_Accuracy = (WeaponStats["hits"] / WeaponStats["shots"]) * 100


        #IF SNIPER, SPY, SOLDIER, DEMO WAS PLAYED
        #ADD OVERALL ENTRY
        if SpecialClass == True:
            Class = 0;
            Airshots = PlayerStats["as"]
            Headshots = PlayerStats["headshots_hit"] + PlayerStats ["headshots"]
            Backstabs = PlayerStats["backstabs"]

            cursor.execute(''' INSERT INTO PlayerStats
            (GameID, SteamID, ClassID, Airshots, Headshots, Backstabs) VALUES
            ({},{},{},{},{},{})
            '''.format(LogID, PlayerID, Class, Airshots, Headshots, Backstabs))

    print("done")

if __name__ == "__main__":
    env = dotenv_values(".env")

    db = mysql.connector.connect(
        host        = "localhost",
        user        = env['MYSQL_USR'],
        password    = env['MYSQL_PWD'],
        database    = env['MYSQL_DB'],
    )
    cursor = db.cursor()
    cursor.autocommit = True

    DBInit(cursor)

    UpdatePlayerNames(cursor)

    #get blacklisted logs
    cursor.execute("SELECT GameID FROM BlacklistGames")
    BlackListedLogs = [i[0] for i in list(cursor.fetchall())]
    print(str(len(BlackListedLogs)) + " blacklisted matches have been recorded.")

    #get already stored logs
    cursor.execute("SELECT GameID FROM Games")
    StoredLogs = [i[0] for i in list(cursor.fetchall())]
    print(len(StoredLogs), " matches already stored in database.\nFetching Logs...", end="")

    UPLOADERS = env['LOG_UPLOADERS'].split(',')
    AllLogs = []
    #Generate list of LogIDs
    for x in UPLOADERS:
        UploaderLogs = json.loads(requests.get("https://logs.tf/api/v1/log?uploader={}".format(x)).text)
        for x in UploaderLogs["logs"]:
            AllLogs.append(x["id"])

    print(len(AllLogs), "Total logs")

    LogsList = set(set(AllLogs).difference(StoredLogs)).difference(BlackListedLogs)
    print(len(LogsList),  "logs to be processed")

    #Add each Log record
    for idx, LogID in enumerate(LogsList, start=1):
        print("Updating log (" + str(idx) + "/" + str(len(LogsList)) + ") - " + str(LogID) + '... ', end='')
        Log = json.loads(requests.get("http://logs.tf/api/v1/log/{}".format(LogID)).text)
        AddGame(LogID, Log, cursor)

    UpdatePlayerNames(cursor)
    db.commit()
    db.close()
