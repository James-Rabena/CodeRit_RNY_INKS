<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// === DATABASE CONFIGURATION FOR INFINITYFREE ===
$db_host = 'sql308.infinityfree.com'; // Replace with your InfinityFree MySQL hostname
$db_user = 'if0_38911492';            // Replace with your InfinityFree MySQL username
$db_pass = '1aT0aibPfUn';             // Replace with your InfinityFree MySQL password
$db_name = 'if0_38911492_fragrancefusion'; // Replace with your InfinityFree database name

// === CREATE CONNECTION ===
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// === CHECK CONNECTION ===
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// === SET UTF-8 CHARACTER SET ===
if (!$conn->set_charset("utf8mb4")) {
    die("Error setting character set: " . $conn->error);
}

// Fetch users
$users = [];
$userQuery = "SELECT id, firstname, lastname FROM users";
$userResult = $conn->query($userQuery);
if ($userResult->num_rows > 0) {
    while ($row = $userResult->fetch_assoc()) {
        $users[$row['id']] = ['first_name' => $row['firstname'], 'last_name' => $row['lastname']];
    }
}

// Fetch purchases
$purchases = [];
$purchaseQuery = "
    SELECT
        c.id AS cart_id,
        c.product_id,
        c.user_id,
        c.created_at AS purchase_date,
        p.name AS product_name
    FROM cart_items c
    INNER JOIN users u ON c.user_id = u.id
    INNER JOIN products p ON c.product_id = p.id
    ORDER BY c.id DESC";
$purchaseResult = $conn->query($purchaseQuery);
if ($purchaseResult->num_rows > 0) {
    while ($row = $purchaseResult->fetch_assoc()) {
        $purchases[] = [
            'cart_id' => $row['cart_id'],
            'product_id' => $row['product_id'],
            'user_id' => $row['user_id'],
            'purchase_date' => $row['purchase_date'],
            'product_name' => $row['product_name'],
            'status' => 'Pending' // Default value since status is not in the table
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admindashboard.css">
    <style>
        /* Basic table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .remove-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 6px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
        }

        .user-dropdown {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .dropdown-button {
            background-color: #007bff;
            color: white;
            padding: 10px 16px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 120px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 4px;
            text-align: left;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {background-color: #ddd;}

        .show {display:block;}

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        header h1 {
            margin: 0;
        }

        header nav a {
            margin-left: 15px;
            text-decoration: none;
            color: #007bff;
        }

        main h2 {
            margin-bottom: 15px;
        }

        footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="user-dropdown">
            <button onclick="toggleDropdown()" class="dropdown-button">
                Hi <?php echo isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Admin'; ?>
            </button>
            <div id="userDropdown" class="dropdown-content">
                <a href="#">Profile</a>
                <a href="index.php?logout=true">Logout</a>
            </div>
        </div>
        <header>
            <h1>Admin Dashboard</h1>
            <nav>
                <a href="index.php">Go to Home</a>
            </nav>
        </header>
        <main>
            <h2>Product Purchases</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Product Name</th>
                        <th>Client Name</th>
                        <th>Purchase Date</th>
                        <th>Status</th>
                        <th>Action</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody id="purchaseTableBody">
                    <?php
                    foreach ($purchases as $purchase) {
                        $orderId = $purchase['cart_id'];
                        $productId = $purchase['product_id'];
                        $userId = $purchase['user_id'];
                        $purchaseDate = $purchase['purchase_date'];
                        $productName = htmlspecialchars($purchase['product_name']); // Use the product name from the $purchases array
                        $status = htmlspecialchars($purchase['status']);

                        if (isset($users[$userId])) {
                            $clientName = htmlspecialchars($users[$userId]['first_name'] . ' ' . $users[$userId]['last_name']);

                            echo "<tr data-order-id='" . $orderId . "'>";
                            echo "<td>" . $orderId . "</td>";
                            echo "<td>" . $productName . "</td>";
                            echo "<td>" . $clientName . "</td>";
                            echo "<td>" . $purchaseDate . "</td>";
                            echo "<td>";
                            echo "<select name='status_" . $orderId . "'>";
                            echo "<option value='Pending' " . ($status === 'Pending' ? 'selected' : '') . ">Pending</option>";
                            echo "<option value='Received' " . ($status === 'Received' ? 'selected' : '') . ">Received</option>";
                            echo "</select>";
                            echo "</td>";
                            echo "<td><button onclick='updateOrder(" . $orderId . ", \"" . $productName . "\", \"" . $clientName . "\")'>Update</button></td>";
                            echo "<td><button class='remove-btn' onclick='removePurchase(" . $orderId . ", this)'>Remove</button></td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </main>
        <footer>
            <p>&copy; <?php echo date("Y"); ?> Fragrance Fusion</p>
        </footer>
    </div>
    <script>
        function toggleDropdown() {
            document.getElementById("userDropdown").classList.toggle("show");
        }

        window.onclick = function(event) {
            if (!event.target.matches('.dropdown-button')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }

        function updateOrder(orderId, productName, purchaserName) {
            const statusDropdown = document.querySelector(`select[name='status_${orderId}']`);
            const newStatus = statusDropdown.value;

            fetch('update_purchase.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `order_id=${orderId}&status=${newStatus}`,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Order status updated successfully for Order ID: ${orderId}`);
                } else {
                    console.error(data.error);
                    alert(`Error: ${data.error}`);
                }
            })
            .catch(error => {
                console.error('There was an error updating the order:', error);
                alert('An error occurred while trying to update the order.');
            });
        }

        function removePurchase(orderId, button) {
            if (confirm('Are you sure you want to remove this order?')) {
                fetch('remove_purchase.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `order_id=${orderId}`,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const row = button.parentNode.parentNode;
                        row.remove();
                        console.log(data.message);
                        // Optionally, provide user feedback (e.g., a success message)
                    } else {
                        console.error(data.error);
                        // Optionally, provide user feedback (e.g., an error message)
                    }
                })
                .catch(error => {
                    console.error('There was an error removing the order:', error);
                    // Optionally, provide user feedback
                });
            }
        }

        // Function to dynamically add a new row to the table
        function addPurchase(newPurchase) {
            const tableBody = document.getElementById('purchaseTableBody');
            const newRow = tableBody.insertRow();
            newRow.setAttribute('data-order-id', newPurchase.cart_id);

            let cell = newRow.insertCell();
            cell.textContent = newPurchase.cart_id;

            cell = newRow.insertCell();
            cell.textContent = newPurchase.product_name;

            cell = newRow.insertCell();
            cell.textContent = newPurchase.client_name;

            cell = newRow.insertCell();
            cell.textContent = newPurchase.purchase_date;

            cell = newRow.insertCell();
            const statusSelect = document.createElement('select');
            statusSelect.name = `status_${newPurchase.cart_id}`;
            const pendingOption = document.createElement('option');
            pendingOption.value = 'Pending';
            pendingOption.textContent = 'Pending';
            statusSelect.appendChild(pendingOption);
            const receivedOption = document.createElement('option');
            receivedOption.value = 'Received';
            receivedOption.textContent = 'Received';
            statusSelect.appendChild(receivedOption);
            cell.appendChild(statusSelect);

            cell = newRow.insertCell();
            const updateButton = document.createElement('button');
            updateButton.textContent = 'Update';
            updateButton.onclick = function() {
                updateOrder(newPurchase.cart_id, newPurchase.product_name, newPurchase.client_name);
            };
            cell.appendChild(updateButton);

            cell = newRow.insertCell();
            const removeButton = document.createElement('button');
            removeButton.className = 'remove-btn';
            removeButton.textContent = 'Remove';
            removeButton.onclick = function() {
                removePurchase(newPurchase.cart_id, this);
            };
            cell.appendChild(removeButton);
        }


    </script>
</body>
</html>
<?php
$conn->close();
?>