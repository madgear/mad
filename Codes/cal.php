
// Sample multidimensional array
var multidimensionalArray = [
  [4, 3, 2],
  [1, 6, 5],
  [9, 8, 7]
];

// Sort the multidimensional array based on the first element of each subarray
multidimensionalArray.sort(function(a, b) {
  return a[0] - b[0];
});

// Output the sorted multidimensional array
console.log(multidimensionalArray);


<script>
$(document).ready(function() {
    // Function to check for event updates
    function checkForUpdates() {
        $.ajax({
            url: 'check_updates.php', // PHP script to check for updates
            type: 'GET',
            success: function(response) {
                if (response.updated) {
                    // Events have been updated, refresh the calendar
                    $('#calendar').fullCalendar('refetchEvents');
                }

                // Continue checking for updates
                checkForUpdates();
            },
            error: function() {
                alert('Failed to check for updates');
            }
        });
    }

    // Start checking for updates
    checkForUpdates();

    // Initialize FullCalendar
    $('#calendar').fullCalendar({
        // Set your calendar options here
        // ...
    });
});
</script>


<?php
// Include your database connection file
include('db_connection.php');

// Get the last update time from the client
$lastUpdate = $_GET['lastUpdate'];

// Query the database to check for updates
$sql = "SELECT MAX(updated_at) AS lastUpdate FROM events";
$result = mysqli_query($connection, $sql);
$row = mysqli_fetch_assoc($result);
$serverUpdate = $row['lastUpdate'];

// Compare the last update times
if ($serverUpdate > $lastUpdate) {
    $response = array('updated' => true);
} else {
    $response = array('updated' => false);
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>

<script>
$(document).ready(function() {
    // Function to fetch and update events
    function updateEvents() {
        $.ajax({
            url: 'fetch_events.php', // PHP script to fetch events
            type: 'GET',
            success: function(response) {
                // Update the events on the calendar
                $('#calendar').fullCalendar('removeEvents');
                $('#calendar').fullCalendar('addEventSource', response);
                $('#calendar').fullCalendar('rerenderEvents');
            },
            error: function() {
                alert('Failed to fetch events');
            }
        });
    }

    // Set up periodic event updates
    setInterval(updateEvents, 5000); // Update every 5 seconds (adjust as needed)

    // Initialize FullCalendar
    $('#calendar').fullCalendar({
        // Set your calendar options here
        // ...
    });
});
</script>



<?php
// Include your database connection file
include('db_connection.php');

// Fetch events from the database
$sql = "SELECT * FROM events";
$result = mysqli_query($connection, $sql);

$events = array();

while ($row = mysqli_fetch_assoc($result)) {
    $events[] = array(
        'id' => $row['id'],
        'title' => $row['title'],
        'start' => $row['start'],
        'end' => $row['end'],
        'room' => $row['room'],
        'teacher' => $row['teacher']
    );
}

// Return the events as JSON
header('Content-Type: application/json');
echo json_encode($events);
?>


<script>
$(document).ready(function() {
    $('#calendar').fullCalendar({
        // Set your calendar options here
        // ...

        // Fetch events from the server
        events: [
            // Add other event sources if needed
            {
                url: 'fetch_events.php', // PHP file to fetch events
                color: 'blue' // Color for regular events
            },
            {
                url: 'fetch_holidays.php', // PHP file to fetch holidays
                color: 'red' // Color for holidays
            }
        ],

        // Handle event rendering
        eventRender: function(event, element) {
            if (event.source.url === 'fetch_holidays.php') {
                element.addClass('holiday-flag');
            }
        }
    });
});
</script>

<style>
.holiday-flag {
    background-color: red;
    color: white;
}
</style>



// Retrieve the event data from the AJAX request
$title = $_POST['title'];
$start = $_POST['start'];
$end = $_POST['end'];
$room = $_POST['room'];
$teacher = $_POST['teacher'];

// Check room availability
$roomQuery = "SELECT * FROM events WHERE room = '$room' AND ((start >= '$start' AND start < '$end') OR (end > '$start' AND end <= '$end'))";
$roomResult = mysqli_query($connection, $roomQuery);
if (mysqli_num_rows($roomResult) > 0) {
    $response = array('status' => 'error', 'message' => 'Room is not available during the specified time');
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Check teacher availability
$teacherQuery = "SELECT * FROM events WHERE teacher = '$teacher' AND ((start >= '$start' AND start < '$end') OR (end > '$start' AND end <= '$end'))";
$teacherResult = mysqli_query($connection, $teacherQuery);
if (mysqli_num_rows($teacherResult) > 0) {
    $response = array('status' => 'error', 'message' => 'Teacher is not available during the specified time');
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Insert the event data into the database
$sql = "INSERT INTO events (title, start, end, room, teacher) VALUES ('$title', '$start', '$end', '$room', '$teacher')";
// ...


<script>
$(document).ready(function() {
    $('#calendar').fullCalendar({
        // Set your calendar options here
        // ...

        // Handle event creation
        select: function(start, end) {
            var title = prompt('Enter event title:');
            var room = prompt('Enter room:');
            var teacher = prompt('Enter teacher:');

            if (title && room && teacher) {
                $.ajax({
                    url: 'add_event.php',
                    type: 'POST',
                    data: {
                        title: title,
                        start: start.format(),
                        end: end.format(),
                        room: room,
                        teacher: teacher
                    },
                    success: function(response) {
                        alert(response.message);
                        $('#calendar').fullCalendar('refetchEvents');
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = xhr.responseJSON.message;
                        alert('Failed to add event: ' + errorMessage);
                    }
                });
            }
        }
    });
});
</script>


<script>
$(document).ready(function() {
    // Populate the select box with available rooms
    $.ajax({
        url: 'get_available_rooms.php',
        type: 'GET',
        success: function(response) {
            var rooms = response.rooms;
            var roomSelect = $('#room-select');

            // Add options to the select box
            $.each(rooms, function(index, room) {
                roomSelect.append($('<option></option>').val(room).text(room));
            });
        },
        error: function() {
            alert('Failed to fetch available rooms');
        }
    });

    // Populate the select box with available teachers
    $.ajax({
        url: 'get_available_teachers.php',
        type: 'GET',
        success: function(response) {
            var teachers = response.teachers;
            var teacherSelect = $('#teacher-select');

            // Add options to the select box
            $.each(teachers, function(index, teacher) {
                teacherSelect.append($('<option></option>').val(teacher).text(teacher));
            });
        },
        error: function() {
            alert('Failed to fetch available teachers');
        }
    });

    $('#calendar').fullCalendar({
        // Set your calendar options here
        // ...

        // Handle event creation
        select: function(start, end) {
            // Use the selected room and teacher from the select boxes
            var room = $('#room-select').val();
            var teacher = $('#teacher-select').val();

            if (room && teacher) {
                // ...
            } else {
                alert('Please select a room and teacher');
            }
        }
    });
});
</script>

<select id="room-select">
    <option value="">Select Room</option>
</select>

<select id="teacher-select">
    <option value="">Select Teacher</option>
</select>

