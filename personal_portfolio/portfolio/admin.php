<?php
session_start();
require 'includes/db.php';

$message = '';
$msgType = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'view';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Record deleted successfully.";
        $msgType = "success";
    } else {
        $message = "Error deleting record.";
        $msgType = "error";
    }
    $stmt->close();
    $action = 'view';
}

// Handle Add / Edit form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $userMessage = trim($_POST['message']);
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE contacts SET name=?, email=?, message=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $userMessage, $id);
        if ($stmt->execute()) {
            $message = "Record updated successfully.";
            $msgType = "success";
            $action = 'view';
        } else {
            $message = "Error updating record.";
            $msgType = "error";
        }
        $stmt->close();
    } else {
        // Create
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $userMessage);
        if ($stmt->execute()) {
            $message = "Record created successfully.";
            $msgType = "success";
            $action = 'view';
        } else {
            $message = "Error creating record.";
            $msgType = "error";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio | Admin panel</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo">MyPortfolio</a>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="admin.php" class="active">Admin</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Admin Panel</h2>
            <div class="card" style="max-width: 1000px; margin: 0 auto; overflow-x: auto;">
                
                <?php if (!empty($message)): ?>
                    <div class="message <?php echo $msgType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($action == 'view'): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h3>Contact Messages (CRUD)</h3>
                        <a href="admin.php?action=add" class="btn btn-sm">Add New Record</a>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC");
                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                    // Truncate message for table view
                                    $msgSnippet = (strlen($row['message']) > 30) ? substr($row['message'], 0, 30) . '...' : $row['message'];
                                    echo "<td>" . htmlspecialchars($msgSnippet) . "</td>";
                                    echo "<td>" . date('Y-m-d', strtotime($row['created_at'])) . "</td>";
                                    echo "<td>
                                            <a href='admin.php?action=edit&id=" . $row['id'] . "' class='btn btn-sm' style='margin-right: 5px; background: #64748b;'>Edit</a>
                                            <a href='admin.php?delete=" . $row['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' style='text-align:center;'>No records found.</td></tr>";
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" style="text-align: right; font-size: 0.85rem; color: var(--text-muted);">End of records list</td>
                            </tr>
                        </tfoot>
                    </table>

                <?php elseif ($action == 'add' || $action == 'edit'): 
                    $editName = '';
                    $editEmail = '';
                    $editMessage = '';
                    $editId = '';
                    $formTitle = 'Add New Record';

                    if ($action == 'edit' && isset($_GET['id'])) {
                        $editId = intval($_GET['id']);
                        $stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ?");
                        $stmt->bind_param("i", $editId);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        if ($row = $res->fetch_assoc()) {
                            $editName = $row['name'];
                            $editEmail = $row['email'];
                            $editMessage = $row['message'];
                            $formTitle = 'Edit Record (ID: ' . $editId . ')';
                        }
                        $stmt->close();
                    }
                ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h3><?php echo $formTitle; ?></h3>
                        <a href="admin.php" class="btn btn-sm" style="background: #64748b;">Back to List</a>
                    </div>
                    
                    <form method="POST" action="admin.php">
                        <input type="hidden" name="id" value="<?php echo $editId; ?>">
                        
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($editName); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($editEmail); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" class="form-control" required><?php echo htmlspecialchars($editMessage); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn"><?php echo $action == 'edit' ? 'Update' : 'Create'; ?> Record</button>
                    </form>

                <?php endif; ?>
                
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> MyPortfolio. All rights reserved.</p>
    </footer>
</body>
</html>
