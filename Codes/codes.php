

<?php
// Database credentials
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check the request type
$requestType = $_SERVER['REQUEST_METHOD'];

switch ($requestType) {
    case 'GET':
        // Fetch events from the database
        $sql = "SELECT id, title, start_date, end_date FROM events";
        $result = $conn->query($sql);

        $events = array();

        // Format events for FullCalendar
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $event = array();
                $event['id'] = $row['id'];
                $event['title'] = $row['title'];
                $event['start'] = $row['start_date'];
                $event['end'] = $row['end_date'];
                $events[] = $event;
            }
        }

        // Return events in JSON format
        echo json_encode($events);
        break;

    case 'POST':
        // Add a new event
        $title = $_POST['title'];
        $start = $_POST['start'];
        $end = $_POST['end'];

        $sql = "INSERT INTO events (title, start_date, end_date) VALUES ('$title', '$start', '$end')";
        $result = $conn->query($sql);

        if ($result === TRUE) {
            echo "Event added successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        break;

    case 'PUT':
        // Update an existing event
        parse_str(file_get_contents("php://input"), $putParams);
        $id = $putParams['id'];
        $title = $putParams['title'];
        $start = $putParams['start'];
        $end = $putParams['end'];

        $sql = "UPDATE events SET title='$title', start_date='$start', end_date='$end' WHERE id=$id";
        $result = $conn->query($sql);

        if ($result === TRUE) {
            echo "Event updated successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        break;

    case 'DELETE':
        // Delete an event
        parse_str(file_get_contents("php://input"), $deleteParams);
        $id = $deleteParams['id'];

        $sql = "DELETE FROM events WHERE id=$id";
        $result = $conn->query($sql);

        if ($result === TRUE) {
            echo "Event deleted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        break;

    default:
        break;
}

$conn->close();
?>

<?php
// Database credentials
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check the request type
$requestType = $_SERVER['REQUEST_METHOD'];

switch ($requestType) {
    case 'GET':
        // Fetch events from the database
        $sql = "SELECT id, title, start_date, end_date FROM events";
        $result = $conn->query($sql);

        $events = array();

        // Format events for FullCalendar
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $event = array();
                $event['id'] = $row['id'];
                $event['title'] = $row['title'];
                $event['start'] = $row['start_date'];
                $event['end'] = $row['end_date'];
                $events[] = $event;
            }
        }

        // Return events in JSON format
        echo json_encode($events);
        break;

    case 'POST':
        // Add a new event
        $title = $_POST['title'];
        $start = $_POST['start'];
        $end = $_POST['end'];
        $roomId = $_POST['room_id'];

        // Check if the room is available
        $sql = "SELECT id FROM events WHERE room_id = $roomId AND ((start_date <= '$start' AND end_date >= '$start') OR (start_date <= '$end' AND end_date >= '$end'))";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "Room not available at the selected time.";
            exit;
        }

        // Insert the event into the database
        $sql = "INSERT INTO events (title, start_date, end_date, room_id) VALUES ('$title', '$start', '$end', $roomId)";
        $result = $conn->query($sql);

        if ($result === TRUE) {
            echo "Event added successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        break;

    case 'PUT':
        // Update an existing event
        parse_str(file_get_contents("php://input"), $putParams);
        $id = $putParams['id'];
        $title = $putParams['title'];
        $start = $putParams['start'];
        $end = $putParams['end'];
        $roomId = $putParams['room_id'];

        // Check if the room is available
        $sql = "SELECT id FROM events WHERE room_id = $roomId AND ((start_date <= '$start' AND end_date >= '$start') OR (start_date <= '$end' AND end_date >= '$end')) AND id != $id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "Room not available at the selected time.";
            exit;
        }

        // Update the event in the database
        $sql = "UPDATE events SET title='$title', start_date='$start', end_date='$end',

room_id=$roomId WHERE id=$id";
        $result = $conn->query($sql);

        if ($result === TRUE) {
            echo "Event updated successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        break;

    case 'DELETE':
        // Delete an event
        parse_str(file_get_contents("php://input"), $deleteParams);
        $id = $deleteParams['id'];

        $sql = "DELETE FROM events WHERE id=$id";
        $result = $conn->query($sql);

        if ($result === TRUE) {
            echo "Event deleted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        break;

    default:
        break;
}

$conn->close();
?>


<?php
// Database credentials
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check the request type
$requestType = $_SERVER['REQUEST_METHOD'];

switch ($requestType) {
    case 'GET':
        // Fetch events from the database
        $sql = "SELECT id, title, start_date, end_date, room_id, teacher_id FROM events";
        $result = $conn->query($sql);

        $events = array();

        // Format events for FullCalendar
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $event = array();
                $event['id'] = $row['id'];
                $event['title'] = $row['title'];
                $event['start'] = $row['start_date'];
                $event['end'] = $row['end_date'];
                $event['room_id'] = $row['room_id'];
                $event['teacher_id'] = $row['teacher_id'];
                $events[] = $event;
            }
        }

        // Return events in JSON format
        echo json_encode($events);
        break;

    case 'POST':
        // Add a new event
        $title = $_POST['title'];
        $start = $_POST['start'];
        $end = $_POST['end'];
        $roomId = $_POST['room_id'];
        $teacherId = $_POST['teacher_id'];

        // Check if the room is available
        $roomSql = "SELECT id FROM events WHERE room_id = $roomId AND ((start_date <= '$start' AND end_date >= '$start') OR (start_date <= '$end' AND end_date >= '$end'))";
        $roomResult = $conn->query($roomSql);

        if ($roomResult->num_rows > 0) {
            echo "Room not available at the selected time.";
            exit;
        }

        // Check if the teacher is available
        $teacherSql = "SELECT id FROM events WHERE teacher_id = $teacherId AND ((start_date <= '$start' AND end_date >= '$start') OR (start_date <= '$end' AND end_date >= '$end'))";
        $teacherResult = $conn->query($teacherSql);

        if ($teacherResult->num_rows > 0) {
            echo "Teacher not available at the selected time.";
            exit;
        }

        // Insert the event into the database
        $sql = "INSERT INTO events (title, start_date, end_date, room_id, teacher_id) VALUES ('$title', '$start', '$end', $roomId, $teacherId)";
        $result = $conn->query($sql);

        if ($result === TRUE) {
            echo "Event added successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        break;

    case 'PUT':
        // Update an existing event
        parse_str(file_get_contents("php://input"), $putParams);
        $id = $putParams['id'];
        $title = $putParams['title'];
        $start =


$putParams['start'];
        $end = $putParams['end'];
        $roomId = $putParams['room_id'];
        $teacherId = $putParams['teacher_id'];

        // Check if the room is available
        $roomSql = "SELECT id FROM events WHERE room_id = $roomId AND ((start_date <= '$start' AND end_date >= '$start') OR (start_date <= '$end' AND end_date >= '$end')) AND id != $id";
        $roomResult = $conn->query($roomSql);

        if ($roomResult->num_rows > 0) {
            echo "Room not available at the selected time.";
            exit;
        }

        // Check if the teacher is available
        $teacherSql = "SELECT id FROM events WHERE teacher_id = $teacherId AND ((start_date <= '$start' AND end_date >= '$start') OR (start_date <= '$end' AND end_date >= '$end')) AND id != $id";
        $teacherResult = $conn->query($teacherSql);

        if ($teacherResult->num_rows > 0) {
            echo "Teacher not available at the selected time.";
            exit;
        }

        // Update the event in the database
        $sql = "UPDATE events SET title='$title', start_date='$start', end_date='$end', room_id=$roomId, teacher_id=$teacherId WHERE id=$id";
        $result = $conn->query($sql);

        if ($result === TRUE) {
            echo "Event updated successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        break;

    case 'DELETE':
        // Delete an event
        parse_str(file_get_contents("php://input"), $deleteParams);
        $id = $deleteParams['id'];

        $sql = "DELETE FROM events WHERE id=$id";
        $result = $conn->query($sql);

        if ($result === TRUE) {
            echo "Event deleted successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        break;

    default:
        break;
}

$conn->close();
?>
