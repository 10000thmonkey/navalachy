<?php













$begin = strtotime("2023-02-01");
$end = strtotime("2023-03-01");
$month = date('n');
$year = date('Y');




$bookings = $nvbk->get_bookings(149, "2023-02-01", "2023-03-01");
print_r($bookings);


$num_days = date('t', strtotime("$year-$month-01"));

echo "<table border='1'>";
echo "<tr>";
echo "<th>Monday</th>";
echo "<th>Tuesday</th>";
echo "<th>Wednesday</th>";
echo "<th>Thursday</th>";
echo "<th>Friday</th>";
echo "<th>Saturday</th>";
echo "<th>Sunday</th>";
echo "</tr>";

$first_day = date("N", mktime(0, 0, 0, $month, 1, $year));
$day = 1;

echo "<tr>";
for ($i = 1; $i < $first_day; $i++) {
	echo "<td>&nbsp;</td>";
}

for ($i = $first_day; $i < $num_days + $first_day; $i++)
{
	if ($i % 7 == 1) {
		echo "</tr><tr>";
	}
	echo "<td>$day</td>";

	// Increment the day counter
	$day++;
}

// Fill in the remaining cells with blank spaces
for ($i = ($num_days + $first_day) % 7; $i <= 7; $i++) {
	echo "<td>&nbsp;</td>";
}

// Close the table
echo "</tr></table>";