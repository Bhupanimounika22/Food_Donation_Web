<?php
require_once 'includes/header.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('donations.php');
}

$donation_id = sanitize_input($_GET['id']);

// Get donation details
$sql = "SELECT fd.*, u.first_name, u.last_name, u.email, u.contact 
        FROM food_donations fd 
        JOIN users u ON fd.donor_id = u.id 
        WHERE fd.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $donation_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    redirect('donations.php');
}

$donation = $result->fetch_assoc();

// Process reservation request
$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reserve'])) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_email'])) {
        // Store the current page as the redirect destination after login
        $_SESSION['redirect_after_login'] = 'donation_details.php?id=' . $donation_id;
        // Redirect to login page
        redirect('login.php');
    }
    
    $recipient_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Default to 1 if using old system
    $notes = isset($_POST['notes']) ? sanitize_input($_POST['notes']) : '';
    
    // Check if donation is still available
    if ($donation['status'] != 'available') {
        $error_message = "Sorry, this donation is no longer available.";
    } else {
        // Insert reservation
        $insert_sql = "INSERT INTO reservations (donation_id, recipient_id, notes) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iis", $donation_id, $recipient_id, $notes);
        
        if ($insert_stmt->execute()) {
            // Update donation status
            $update_sql = "UPDATE food_donations SET status = 'reserved' WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $donation_id);
            $update_stmt->execute();
            
            $success_message = "You have successfully reserved this donation. The donor will be notified.";
            
            // Refresh donation data
            $stmt->execute();
            $result = $stmt->get_result();
            $donation = $result->fetch_assoc();
        } else {
            $error_message = "Error: " . $insert_stmt->error;
        }
    }
}
?>

<div class="bg-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (!empty($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline"><?php echo $success_message; ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline"><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>
        
        <div class="flex flex-col md:flex-row -mx-4">
            <!-- Left Column - Image -->
            <div class="md:w-1/2 px-4 mb-8 md:mb-0">
                <div class="bg-purple-100 rounded-lg overflow-hidden shadow-md h-96 flex items-center justify-center">
                    <?php if (!empty($donation['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($donation['image_url']); ?>" alt="<?php echo htmlspecialchars($donation['food_name']); ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <?php if ($donation['food_type'] == 'raw_food'): ?>
                            <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Raw Food" class="w-full h-full object-cover">
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Cooked Food" class="w-full h-full object-cover">
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Right Column - Details -->
            <div class="md:w-1/2 px-4">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($donation['food_name']); ?></h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $donation['food_type'] == 'raw_food' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>">
                        <?php echo $donation['food_type'] == 'raw_food' ? 'Raw Food' : 'Cooked Food'; ?>
                    </span>
                </div>
                
                <div class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-2">Description</h2>
                    <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($donation['food_details'])); ?></p>
                </div>
                
                <div class="border-t border-gray-200 py-6 space-y-4">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">Donor</p>
                            <p class="font-medium"><?php echo htmlspecialchars($donation['first_name'] . ' ' . $donation['last_name']); ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">Expiry Time</p>
                            <p class="font-medium"><?php echo htmlspecialchars($donation['expiry_time']); ?></p>
                        </div>
                    </div>
                    
                    <?php if ($donation['food_type'] == 'raw_food' && !empty($donation['quantity'])): ?>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Quantity</p>
                                <p class="font-medium"><?php echo htmlspecialchars($donation['quantity']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($donation['food_type'] == 'cooked_food' && !empty($donation['serves_people'])): ?>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Serves</p>
                                <p class="font-medium"><?php echo htmlspecialchars($donation['serves_people']); ?> people</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">Pickup Address</p>
                            <p class="font-medium"><?php echo nl2br(htmlspecialchars($donation['pickup_address'])); ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">Contact Number</p>
                            <p class="font-medium"><?php echo htmlspecialchars($donation['contact_number']); ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">Posted On</p>
                            <p class="font-medium"><?php echo date('F j, Y, g:i a', strtotime($donation['created_at'])); ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <p class="font-medium">
                                <?php if ($donation['status'] == 'available'): ?>
                                    <span class="text-green-600">Available</span>
                                <?php elseif ($donation['status'] == 'reserved'): ?>
                                    <span class="text-yellow-600">Reserved</span>
                                <?php elseif ($donation['status'] == 'completed'): ?>
                                    <span class="text-blue-600">Completed</span>
                                <?php else: ?>
                                    <span class="text-red-600">Expired</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <?php if ($donation['status'] == 'available'): ?>
                    <div class="mt-6">
                        <button type="button" onclick="toggleReservationForm()" class="w-full flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            Reserve This Food
                        </button>
                        
                        <div id="reservationForm" class="hidden mt-6 bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Reservation Form</h3>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $donation_id); ?>" method="post">
                                <div class="mb-4">
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes for the Donor (Optional)</label>
                                    <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Introduce yourself and provide any additional information the donor might need to know."></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button type="button" onclick="toggleReservationForm()" class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        Cancel
                                    </button>
                                    <button type="submit" name="reserve" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                        Confirm Reservation
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php elseif ($donation['status'] == 'reserved'): ?>
                    <div class="mt-6 bg-yellow-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-yellow-600 font-medium">This food has already been reserved by someone else.</p>
                        </div>
                    </div>
                <?php elseif ($donation['status'] == 'completed'): ?>
                    <div class="mt-6 bg-blue-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-blue-600 font-medium">This food donation has been completed.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mt-6 bg-red-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-red-600 font-medium">This food donation has expired.</p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="mt-8">
                    <a href="donations.php" class="text-purple-600 hover:text-purple-500 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Back to Donations
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleReservationForm() {
        const form = document.getElementById('reservationForm');
        form.classList.toggle('hidden');
    }
</script>

<?php
require_once 'includes/footer.php';
?>