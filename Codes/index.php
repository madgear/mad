document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    // ... other options ...

    events: [
      // Weekly Repeating Event
      {
        id: 'weeklyEvent',
        title: 'Weekly Event',
        start: '2023-06-01T10:00:00',
        end: '2023-06-01T11:00:00',
        daysOfWeek: [2], // Recur on Tuesday
        startTime: '10:00:00',
        endTime: '11:00:00',
        color: 'blue',
        textColor: 'white',
      },

      // Biweekly Repeating Event
      {
        id: 'biweeklyEvent',
        title: 'Biweekly Event',
        start: '2023-06-01T14:00:00',
        end: '2023-06-01T15:00:00',
        daysOfWeek: [4], // Recur on Thursday
        startTime: '14:00:00',
        endTime: '15:00:00',
        color: 'green',
        textColor: 'white',
        duration: { weeks: 2 } // Repeat every 2 weeks
      },

      // Monthly Repeating Event
      {
        id: 'monthlyEvent',
        title: 'Monthly Event',
        start: '2023-06-01T16:00:00',
        end: '2023-06-01T17:00:00',
        startTime: '16:00:00',
        endTime: '17:00:00',
        color: 'purple',
        textColor: 'white',
        startRecur: '2023-06-01', // Start recurring from June 1, 2023
        endRecur: '2023-12-31', // End recurring on December 31, 2023
        daysOfMonth: [15] // Recur on the 15th day of each month
      },

      // Bimonthly Repeating Event
      {
        id: 'bimonthlyEvent',
        title: 'Bimonthly Event',
        start: '2023-06-01T18:00:00',
        end: '2023-06-01T19:00:00',
        startTime: '18:00:00',
        endTime: '19:00:00',
        color: 'orange',
        textColor: 'white',
        startRecur: '2023-06-01', // Start recurring from June 1, 2023
        endRecur: '2023-12-31', // End recurring on December 31, 2023
        interval: 2 // Repeat every 2 months
      }
    ],

    // ... other event handling code ...
  });

  calendar.render();
});



<?php
// Assuming you have established a database connection

// Parse the event data received from the AJAX request
$eventData = $_POST['eventData'];
$title = $eventData['title'];
$start = $eventData['start'];
$end = $eventData['end'];
$recurringPattern = $eventData['recurringPattern'];

// Insert the event series into the database
$query = "INSERT INTO events (title, start, end, recurring_pattern) VALUES ('$title', '$start', '$end', '$recurringPattern')";
$result = mysqli_query($connection, $query);

// Get the ID of the event series
$eventSeriesId = mysqli_insert_id($connection);

// Calculate the individual occurrences based on the recurring pattern
// Here, you would implement the logic to generate the individual occurrences based on the recurring pattern and insert them into the database as separate records associated with the event series ID

// Close the database connection
mysqli_close($connection);

// Return a response to the AJAX request
$response = array(
  'success' => true,
  'message' => 'Recurring event added successfully'
);
echo json_encode($response);
?>


<?php
// Assuming you have established a database connection

// Retrieve the event series ID from the AJAX request
$eventSeriesId = $_POST['eventSeriesId'];

// Delete all occurrences associated with the event series ID
$query = "DELETE FROM events WHERE event_series_id = $eventSeriesId";
$result = mysqli_query($connection, $query);

// Delete the event series itself
$query = "DELETE FROM event_series WHERE id = $eventSeriesId";
$result = mysqli_query($connection, $query);

// Close the database connection
mysqli_close($connection);

// Return a response to the AJAX request
$response = array(
  'success' => true,
  'message' => 'Recurring event deleted successfully'
);
echo json_encode($response);
?>
