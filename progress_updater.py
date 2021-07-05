import json, requests, mysql.connector, sys
from steam.steamid import SteamID
from dotenv import dotenv_values
from datetime import datetime
import time

WINDOW = 6*60*60*24*30
MIN_MATCHES = 10

def DBInit(cursor):
    print('updating/creating db...',end='')
    cursor.execute("""
    CREATE TABLE IF NOT EXISTS Progress(
        ProgressID INT UNSIGNED NOT NULL AUTO_INCREMENT,
        EndDate INT UNSIGNED,
        SteamID BIGINT UNSIGNED,
        Matches INT,
        Hours DOUBLE,
        Kills DOUBLE,
        Deaths DOUBLE,
        Assists DOUBLE,
        Backstabs DOUBLE,
        Headshots DOUBLE,
        Airshots DOUBLE,
        DPM DOUBLE,
        DTM DOUBLE,
        HRM DOUBLE,
        PRIMARY KEY (ProgressID),
        CONSTRAINT `fk_progress_steamid`
            FOREIGN KEY (SteamID) REFERENCES Players (SteamID)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    )ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;
    """)
    print('done')
    return

if __name__ == "__main__":
    env = dotenv_values(".env")
    db = mysql.connector.connect(
        host        = "localhost",
        user        = env['MYSQL_USR'],
        password    = env['MYSQL_PWD'],
        database    = env['MYSQL_DB'],
    )
    cursor = db.cursor(buffered=True)
    cursor.autocommit = True
    DBInit(cursor)

    #fetch first game
    cursor.execute("SELECT MIN(Date) FROM Games")
    first_game = int(cursor.fetchall()[0][0])
    now = time.time()

    count = first_game + 60*60*24*30;
    print('starting: ',datetime.utcfromtimestamp(count).strftime('%Y-%m-%d %H:%M:%S'), ' -> ', first_game)
    print('ending: ',datetime.utcfromtimestamp(now).strftime('%Y-%m-%d %H:%M:%S'), ' -> ', count)

    WINDOW
    MIN_MATCHES

    idx = 1
    while(count < now):
        low = count - WINDOW;
        print(idx, 'up to ',datetime.utcfromtimestamp(count).strftime('%Y-%m-%d'))
        res = cursor.execute("""
        SELECT p.SteamID, a.Matches, ROUND(b.Hours, 1) Hours, ROUND(IFNULL(c.Kills, 0), 2) AS 'Kills', ROUND(IFNULL(c.Deaths, 0),2) AS 'Deaths', ROUND(IFNULL(c.Assists, 0),2) AS 'Assists', ROUND(IFNULL(e.Backstabs, 0),1) AS 'Backstabs', ROUND(IFNULL(f.Headshots, 0),1) AS 'Headshots', ROUND(IFNULL(d.Airshots, 0),2) AS 'Airshots', ROUND(IFNULL(c.DPM, 0),2) AS 'DPM', ROUND(a.DTM, 2) DTM, ROUND(a.HRM ,2) HRM FROM Players p
        LEFT JOIN
        (
                SELECT p.SteamID, COUNT(p.GameID) AS 'Matches', SUM(p.DamageTaken) * 60 / SUM(g.Duration) AS 'DTM', SUM(p.HealsReceived) * 60 / SUM(g.Duration) AS 'HRM' FROM Games g
                JOIN PlayerStats p ON g.GameID = p.GameID
          WHERE Date > {low} AND Date < {high}
                GROUP BY p.SteamID
        ) AS a ON a.SteamID = p.SteamID
        LEFT JOIN
        (
                SELECT p.SteamID, SUM(c.Playtime) / 3600 AS 'Hours' FROM Games g
                JOIN PlayerStats p ON g.GameID = p.GameID
                JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
          WHERE Date > {low} AND Date < {high}
          GROUP BY p.SteamID
        ) AS b ON b.SteamID = p.SteamID
        LEFT JOIN
        (
                SELECT p.SteamID, SUM(c.Kills) / Count(DISTINCT(g.GameID)) AS 'Kills', SUM(c.Deaths) / Count(DISTINCT(g.GameID)) AS 'Deaths', SUM(c.Assists) / Count(DISTINCT(g.GameID)) AS 'Assists', SUM(c.Damage) * 60 / SUM(c.Playtime) AS 'DPM' FROM Games g
                JOIN PlayerStats p ON g.GameID = p.GameID
                JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
                WHERE c.ClassID != 7 AND Date > {low} AND Date < {high}
                GROUP BY p.SteamID
        ) AS c ON c.SteamID = p.SteamID
        LEFT JOIN
        (
                SELECT p.SteamID, Sum(Airshots) / Count(DISTINCT(g.GameID)) AS 'Airshots' FROM Games g
                JOIN PlayerStats p ON g.GameID = p.GameID
                JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
                WHERE Date > {low} AND Date < {high} AND ClassID IN (2,4)
                GROUP BY p.SteamID
        ) AS d ON d.SteamID = p.SteamID
        LEFT JOIN
        (
                SELECT p.SteamID, Sum(Backstabs) / Count(g.GameID)  AS 'Backstabs' FROM Games g
                JOIN PlayerStats p ON g.GameID = p.GameID
                JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
                WHERE ClassID = 9 AND Date > {low} AND Date < {high}
                GROUP BY p.SteamID
        ) AS e ON e.SteamID = p.SteamID
        LEFT JOIN
        (
                SELECT p.SteamID, Sum(Headshots) / Count(g.GameID)  AS 'Headshots' FROM Games g
                JOIN PlayerStats p ON g.GameID = p.GameID
                JOIN ClassStats c ON c.PlayerStatsID = p.PlayerStatsID
                WHERE ClassID = 8 AND Date > {low} AND Date < {high}
                GROUP BY p.SteamID
        ) AS f ON f.SteamID = p.SteamID
        HAVING Matches > {min_matches}
        ORDER BY DPM DESC
        """.format(low=low, high=count, min_matches=MIN_MATCHES))
        res = cursor.fetchall()
        for r in res:
            cursor.execute("""INSERT INTO Progress
            (EndDate, SteamID, Matches, Hours, Kills, Deaths, Assists, Backstabs, Headshots, Airshots, DPM, DTM, HRM)
            VALUES
            ({},{},{},{},{},{},{},{},{},{},{},{},{})
            """.format(count, r[0], r[1],r[2],r[3],r[4],r[5],r[6],r[7],r[8],r[9],r[10],r[11]))
        idx += 1
        count += 60*60*24*30;

    db.commit()
