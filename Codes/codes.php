DbSchema
Easy Query Builder (EQB)
Valentina Studio
FlySpeed SQL Query


<?php
// Assuming you have established a database connection

// Parse the event data received from the AJAX request
$eventData = $_POST['eventData'];
$title = $eventData['title'];
$start = $eventData['start'];
$end = $eventData['end'];
$recurringPattern = $eventData['recurringPattern'];

// Generate a list of specific dates based on the recurring pattern
$dates = []; // Array to store the specific dates

// Generate the specific dates based on the recurring pattern
// Modify this logic based on your specific recurring pattern
// For example, generating weekly occurrences for 4 weeks
$startDate = new DateTime($start);
$endDate = new DateTime($end);
$interval = new DateInterval('P1W'); // Weekly interval
$dateRange = new DatePeriod($startDate, $interval, $endDate);
foreach ($dateRange as $date) {
    $dates[] = $date->format('Y-m-d');
}

// Insert each occurrence as a separate entry in the events table
foreach ($dates as $date) {
    $query = "INSERT INTO events (title, start, end) VALUES ('$title', '$date $start', '$date $end')";
    mysqli_query($connection, $query);
}

// Close the database connection
mysqli_close($connection);

// Return a response to the AJAX request
$response = array(
    'success' => true,
    'message' => 'Recurring events added successfully'
);
echo json_encode($response);
?>


<?php
// Assuming you have established a database connection

// Parse the event data received from the AJAX request
$eventData = $_POST['eventData'];
$eventId = $eventData['id'];

// Perform the necessary updates or deletions on the specific occurrence in the events table
// Modify the following queries based on your requirements
$updateQuery = "UPDATE events SET ... WHERE id = $eventId";
$deleteQuery = "DELETE FROM events WHERE id = $eventId";

// Execute the update or delete query based on the operation
if ($eventData['action'] === 'update') {
    mysqli_query($connection, $updateQuery);
} elseif ($eventData['action'] === 'delete') {
    mysqli_query($connection, $deleteQuery);
}

// Close the database connection
mysqli_close($connection);

// Return a response to the AJAX request
$response = array(
    'success' => true,
    'message' => 'Event updated/deleted successfully'
);
echo json_encode($response);
?>


// Assuming you have initialized FullCalendar and have the necessary event handlers

// Handle the creation of a recurring event
function handleRecurringEventCreation(eventData) {
  // Parse the event data and generate the list of specific dates
  const { title, start, end, recurringPattern } = eventData;
  const dates = generateSpecificDates(start, end, recurringPattern);

  // Send AJAX requests to insert each occurrence as a separate entry
  dates.forEach(date => {
    const eventData = { title, start: date + ' ' + start, end: date + ' ' + end };
    createEvent(eventData);
  });
}

// Function to generate specific dates based on the recurring pattern
function generateSpecificDates(start, end, recurringPattern) {
  // Modify this logic based on your specific recurring pattern
  // For example, generating weekly occurrences for 4 weeks
  const startDate = moment(start);
  const endDate = moment(end);
  const dates = [];

  let currentDate = startDate.clone();
  while (currentDate.isSameOrBefore(endDate)) {
    dates.push(currentDate.format('YYYY-MM-DD'));
    currentDate.add(1, 'week'); // Weekly interval
  }

  return dates;
}

// Function to send AJAX request to create an event
function createEvent(eventData) {
  $.ajax({
    url: 'create-event.php',
    method: 'POST',
    data: { eventData },
    dataType: 'json',
    success: function(response) {
      // Handle the success response
      console.log(response.message);
    },
    error: function(xhr, status, error) {
      // Handle the error response
      console.error(error);
    }
  });
}

// Attach the handler to the appropriate FullCalendar event creation method
calendar.addEvent = handleRecurringEventCreation;



// Assuming you have initialized FullCalendar and have the necessary event handlers

// Handle the update or deletion of an individual occurrence
function handleIndividualOccurrenceUpdate(eventData) {
  const { id, action } = eventData;

  // Send AJAX request to update or delete the individual occurrence
  $.ajax({
    url: 'update-event.php',
    method: 'POST',
    data: { eventData },
    dataType: 'json',
    success: function(response) {
      // Handle the success response
      console.log(response.message);
      if (action === 'delete') {
        // Remove the event from the calendar if it was deleted
        calendar.getEventById(id).remove();
      }
    },
    error: function(xhr, status, error) {
      // Handle the error response
      console.error(error);
    }
  });
}

// Attach the handler to the appropriate FullCalendar event update or deletion method
calendar.eventClick = handleIndividualOccurrenceUpdate;
