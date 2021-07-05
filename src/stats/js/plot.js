$(document).ready(function () {
    showGraph();
});

function $_GET(q,s) {
    s = (s) ? s : window.location.search;
    var re = new RegExp('&amp;'+q+'=([^&amp;]*)','i');
    return (s=s.replace(/^\?/,'&amp;').match(re)) ?s=s[1] :s='';
}
function showGraph()
{
  var id = $_GET('steamid');
  console.log(id);
  {
    $.post("data.php", {steamid: id},
    function (data)
    {
      const info = JSON.parse(data);
      var upTill = [];
      var Dmg = [];
      var kills = [];
      var headshots= [];
      var airshots =[];
      
      for (var i in info) {
        upTill.push(new Date(info[i].EndDate*1000).toLocaleDateString("en-US"));
        Dmg.push(info[i].DPM);
        kills.push(info[i].Kills);
        headshots.push(info[i].Headshots);
        airshots.push(info[i].Airshots);
      }

      var chartdata = {
        labels: upTill,
        datasets: [
          {
            label: 'DPM',
            backgroundColor: '#49e2ff',
            borderColor: '#46d5f1',
            hoverBackgroundColor: '#CCCCCC',
            hoverBorderColor: '#666666',
            data: Dmg
          },
          {
            label: 'Kills',
            backgroundColor: '#ffffff',
            borderColor: '#ffffff',
            hoverBackgroundColor: '#ffffff',
            hoverBorderColor: '#ffffff',
            data: kills
          },
          {
            label: 'Headshots',
            backgroundColor: '#69a5ff',
            borderColor: '#69a5ff',
            hoverBackgroundColor: '#69a5ff',
            hoverBorderColor: '#69a5ff',
            data: headshots
          },
          {
            label: 'Airshots',
            backgroundColor: '#8bff5c',
            borderColor: '#8bff5c',
            hoverBackgroundColor: '#8bff5c',
            hoverBorderColor: '#8bff5c',
            data: airshots
          } 
        ]
      };

      var graphTarget = $("#graphCanvas");

      var barGraph = new Chart(graphTarget, {
          type: 'line',
          data: chartdata
      });
    });
  }
}
