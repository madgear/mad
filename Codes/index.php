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



eventDrop: function(info) {
  var event = info.event;
  
  // Retrieve the updated start and end dates of the event
  var newStart = event.start;
  var newEnd = event.end;

  // If the event is a recurring event, update the corresponding occurrences in the database
  if (event.isRecurring()) {
    var eventSeriesId = event.extendedProps.eventSeriesId;
    
    // Send an AJAX request to your PHP script to update the occurrences in the database
    // Include the eventSeriesId, newStart, and newEnd in the request
    
    // Example AJAX request using jQuery:
    $.ajax({
      url: 'update_recurring_events.php',
      type: 'POST',
      data: {
        eventSeriesId: eventSeriesId,
        newStart: newStart,
        newEnd: newEnd
      },
      success: function(response) {
        // Handle the success response
        console.log('Recurring events updated successfully');
      },
      error: function(xhr, status, error) {
        // Handle the error response
        console.error('Error updating recurring events: ' + error);
      }
    });
  }
}








eventReceive: function(info) {
  var event = info.event;
  
  // Retrieve the start and end dates of the newly created event
  var start = event.start;
  var end = event.end;

  // If the event is a recurring event, insert the corresponding occurrences into the database
  if (event.isRecurring()) {
    var eventSeriesId = event.extendedProps.eventSeriesId;
    
    // Send an AJAX request to your PHP script to insert the occurrences into the database
    // Include the eventSeriesId, start, and end in the request
    
    // Example AJAX request using jQuery:
    $.ajax({
      url: 'insert_recurring_events.php',
      type: 'POST',
      data: {
        eventSeriesId: eventSeriesId,
        start: start,
        end: end
      },
      success: function(response) {
        // Handle the success response
        console.log('Recurring events inserted successfully');
      },
      error: function(xhr, status, error) {
        // Handle the error response
        console.error('Error inserting recurring events: ' + error);
      }
    });
  }
}


<?php
// Assuming you have established a database connection

// Parse the event data received from the AJAX request
$eventData = $_POST['eventData'];
$title = $eventData['title'];
$start = $eventData['start'];
$end = $eventData['end'];
$recurringPattern = $eventData['recurringPattern'];

// Insert the event series into the database
$query = "INSERT INTO event_series (title, recurring_pattern) VALUES ('$title', '$recurringPattern')";
$result = mysqli_query($connection, $query);

// Get the ID of the event series
$eventSeriesId = mysqli_insert_id($connection);

// Close the database connection
mysqli_close($connection);

// Return a response to the AJAX request
$response = array(
  'success' => true,
  'message' => 'Recurring event series added successfully',
  'eventSeriesId' => $eventSeriesId
);
echo json_encode($response);
?>
