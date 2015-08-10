<?php
// 


require('../config.php');
require('../db_lib.php');
$db = new db();

// Get total number of mentions
$query = "SELECT count(*) AS cnt
	FROM tweet_mentions";
$results = $db->select($query);
$row = mysqli_fetch_assoc($results);
$total_mentions = $row['cnt'];

// Get number of mentions per day of week
require('../get_mention_days.php');
$tweet_days = get_mention_days();

// Create a string of data points for the Google chart
$day_chart_data = '';
foreach($tweet_days as $data){
	$tweet_day = $data['tweet_day'];
	$cnt = $data['cnt']/$total_mentions;
	$day_chart_data .= "['$tweet_day',$cnt],";		
}
// Clip off trailing comma
$day_chart_data = substr($day_chart_data,0,strlen($day_chart_data)-1);

// Get number of mentions per hour of day
require('../get_mention_hours.php');
$tweet_hours = get_mention_hours();

// Create a string of data points for the Google chart
$hour_chart_data = '';
foreach($tweet_hours as $data){
	$tweet_hour = $data['tweet_hour'];
	$cnt = $data['cnt']/$total_mentions;
	$hour_chart_data .= "['$tweet_hour',$cnt],";		
}
$hour_chart_data = substr($hour_chart_data,0,strlen($hour_chart_data)-1);

// Display report page with <div>s to hold charts
require('page_top.html');
print '<h2>Mention Timing</h2>';
print '% of Mentions per day';
print '<div id="day_chart"></div>';

print "% of Mentions per hour (ET)";
print  '<div id="hour_chart"></div>';

?>
    
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">

  // Load the Visualization API and the piechart package.
  google.load('visualization', '1.0', {'packages':['corechart']});

  // Set a callback to run when the Google Visualization API is loaded.
  google.setOnLoadCallback(drawDayChart);
  google.setOnLoadCallback(drawHourChart);
   
  function drawDayChart() {

    // Create the data table.
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Day');
    data.addColumn('number', 'Mentions');
    data.addRows([ <?php print $day_chart_data; ?> ]);

    // Set chart options
    var options = {'width':500,
                   'height':400};

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.ColumnChart(document.getElementById('day_chart'));
    chart.draw(data, options);
  }
  
    function drawHourChart() {

    // Create the data table.
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Hour');
    data.addColumn('number', 'Mentions');
    data.addRows([ <?php print $hour_chart_data; ?> ]);

    // Set chart options
    var options = {'width':1000,
                   'height':400};

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.ColumnChart(document.getElementById('hour_chart'));
    chart.draw(data, options);
  }
</script>

<?php
require('page_bottom.html');
?>