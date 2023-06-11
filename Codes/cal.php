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

