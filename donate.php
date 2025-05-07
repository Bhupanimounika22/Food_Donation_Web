<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_email'])) {
    // Store the current page as the redirect destination after login
    $_SESSION['redirect_after_login'] = 'donate.php';
    // Redirect to login page
    redirect('login.php');
}

$success_message = '';
$error_message = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug information
    error_log("Form submitted: " . print_r($_POST, true));
    
    // Get form data
    $donor_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Default to 1 if using old system
    error_log("Donor ID: " . $donor_id);
    
    $food_type = sanitize_input($_POST['food_type']);
    $food_name = sanitize_input($_POST['food_name']);
    $food_details = sanitize_input($_POST['food_details']);
    $quantity = isset($_POST['quantity']) ? sanitize_input($_POST['quantity']) : null;
    $serves_people = isset($_POST['serves_people']) ? sanitize_input($_POST['serves_people']) : null;
    $expiry_time = sanitize_input($_POST['expiry_time']);
    $pickup_address = sanitize_input($_POST['pickup_address']);
    $contact_number = sanitize_input($_POST['contact_number']);
    
    // Handle file upload if present
    $image_url = '';
    if (isset($_FILES['food_image']) && $_FILES['food_image']['error'] == 0) {
        $upload_dir = 'uploads/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['food_image']['name']);
        $target_file = $upload_dir . $file_name;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES['food_image']['tmp_name']);
        if ($check !== false) {
            // Try to upload file
            if (move_uploaded_file($_FILES['food_image']['tmp_name'], $target_file)) {
                $image_url = $target_file;
            } else {
                $error_message = "Sorry, there was an error uploading your file.";
            }
        } else {
            $error_message = "File is not an image.";
        }
    }
    
    // Validate input
    if (empty($food_name) || empty($food_details) || empty($expiry_time) || empty($pickup_address) || empty($contact_number)) {
        $error_message = "All required fields must be filled out";
    } else {
        // Insert into food_donations table
        $sql = "INSERT INTO food_donations (donor_id, food_type, food_name, food_details, quantity, serves_people, expiry_time, pickup_address, contact_number, image_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssissss", $donor_id, $food_type, $food_name, $food_details, $quantity, $serves_people, $expiry_time, $pickup_address, $contact_number, $image_url);
        
        if ($stmt->execute()) {
            $success_message = "Your food donation has been listed successfully!";
            error_log("Donation saved successfully with ID: " . $stmt->insert_id);
            // Clear form data
            $_POST = array();
        } else {
            $error_message = "Error: " . $stmt->error;
            error_log("Error saving donation: " . $stmt->error);
        }
    }
}
?>

<div class="bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="inline-block mb-4">
                <div class="flex items-center justify-center w-16 h-16 mx-auto bg-gray-200 rounded-full">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/Emblem_of_India.svg" class="w-10 h-10" alt="Government Emblem">
                </div>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 sm:text-4xl uppercase">
                Food Donation Form
            </h2>
            <div class="w-24 h-1 mx-auto mt-4 mb-6 bg-gray-800"></div>
            <p class="mt-4 text-lg text-gray-600">
                Official portal for food donation under the Ministry of Food Distribution, Government of India
            </p>
        </div>
        
        <div class="mt-12">
            <?php if (!empty($success_message)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6 shadow-sm" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm"><?php echo $success_message; ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6 shadow-sm" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm"><?php echo $error_message; ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="bg-white overflow-hidden shadow-lg border border-gray-300">
                <div class="px-6 py-6 bg-gray-800 sm:px-8">
                    <h3 class="text-xl leading-6 font-bold text-white uppercase flex items-center">
                        <i class="fas fa-file-alt mr-2"></i> Donation Details
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-300">
                        Please provide accurate information about the food you wish to donate. All fields marked with * are mandatory.
                    </p>
                </div>
                
                <div class="px-6 py-6 sm:p-8">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" class="space-y-6">
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="food_type" class="block text-sm font-medium text-gray-700">Food Type</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-utensils text-purple-400"></i>
                                    </div>
                                    <select id="food_type" name="food_type" class="pl-10 py-3 shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-lg transition-all duration-200" onchange="toggleFields()">
                                        <option value="raw_food" <?php echo isset($_POST['food_type']) && $_POST['food_type'] == 'raw_food' ? 'selected' : ''; ?>>Raw Food</option>
                                        <option value="cooked_food" <?php echo isset($_POST['food_type']) && $_POST['food_type'] == 'cooked_food' ? 'selected' : ''; ?>>Cooked Food</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="food_name" class="block text-sm font-medium text-gray-700">Food Name</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-hamburger text-purple-400"></i>
                                    </div>
                                    <input type="text" id="food_name" name="food_name" value="<?php echo isset($_POST['food_name']) ? htmlspecialchars($_POST['food_name']) : ''; ?>" class="pl-10 py-3 shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-lg transition-all duration-200" placeholder="E.g., Rice, Bread, Curry" required>
                                </div>
                            </div>
                            
                            <div class="sm:col-span-6">
                                <label for="food_details" class="block text-sm font-medium text-gray-700">Food Details</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                        <i class="fas fa-info-circle text-purple-400"></i>
                                    </div>
                                    <textarea id="food_details" name="food_details" rows="3" class="pl-10 shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-lg transition-all duration-200" placeholder="Describe the food items, including any allergens or dietary information" required><?php echo isset($_POST['food_details']) ? htmlspecialchars($_POST['food_details']) : ''; ?></textarea>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Brief description of the food items, including any allergens or dietary information.</p>
                            </div>
                            
                            <div class="sm:col-span-3 quantity-field">
                                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity (for raw food)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-weight text-purple-400"></i>
                                    </div>
                                    <input type="text" id="quantity" name="quantity" value="<?php echo isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : ''; ?>" class="pl-10 py-3 shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-lg transition-all duration-200" placeholder="E.g., 5kg rice, 2kg lentils">
                                </div>
                                <p class="mt-2 text-sm text-gray-500">E.g., "5kg rice, 2kg lentils"</p>
                            </div>
                            
                            <div class="sm:col-span-3 serves-field" style="display: none;">
                                <label for="serves_people" class="block text-sm font-medium text-gray-700">Serves (for cooked food)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-users text-purple-400"></i>
                                    </div>
                                    <input type="number" id="serves_people" name="serves_people" value="<?php echo isset($_POST['serves_people']) ? htmlspecialchars($_POST['serves_people']) : ''; ?>" class="pl-10 py-3 shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-lg transition-all duration-200" placeholder="Number of people">
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Approximate number of people this food can serve</p>
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="expiry_time" class="block text-sm font-medium text-gray-700">Expiry Time</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-clock text-purple-400"></i>
                                    </div>
                                    <input type="text" id="expiry_time" name="expiry_time" value="<?php echo isset($_POST['expiry_time']) ? htmlspecialchars($_POST['expiry_time']) : ''; ?>" class="pl-10 py-3 shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-lg transition-all duration-200" placeholder="E.g., Today until 9 PM" required>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">E.g., "Today until 9 PM" or "No expiry"</p>
                            </div>
                            
                            <div class="sm:col-span-6">
                                <label for="pickup_address" class="block text-sm font-medium text-gray-700">Pickup Address</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                        <i class="fas fa-map-marker-alt text-purple-400"></i>
                                    </div>
                                    <textarea id="pickup_address" name="pickup_address" rows="2" class="pl-10 shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-lg transition-all duration-200" placeholder="Enter the complete address where the food can be picked up" required><?php echo isset($_POST['pickup_address']) ? htmlspecialchars($_POST['pickup_address']) : ''; ?></textarea>
                                </div>
                            </div>
                            
                            <div class="sm:col-span-3">
                                <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-purple-400"></i>
                                    </div>
                                    <input type="text" id="contact_number" name="contact_number" value="<?php echo isset($_POST['contact_number']) ? htmlspecialchars($_POST['contact_number']) : ''; ?>" class="pl-10 py-3 shadow-sm focus:ring-purple-500 focus:border-purple-500 block w-full sm:text-sm border-gray-300 rounded-lg transition-all duration-200" placeholder="Your contact number" required>
                                </div>
                            </div>
                            
                            <div class="sm:col-span-6">
                                <label for="food_image" class="block text-sm font-medium text-gray-700">Food Image (Optional)</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-purple-200 border-dashed rounded-lg hover:bg-purple-50 transition-all duration-200">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-purple-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500 px-3 py-2 border border-purple-300 shadow-sm">
                                                <span>Upload a file</span>
                                                <input id="file-upload" name="food_image" type="file" class="sr-only" accept="image/*">
                                            </label>
                                            <p class="pl-1 flex items-center">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            PNG, JPG, GIF up to 10MB
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-5">
                            <div class="flex justify-between items-center">
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i> By submitting this form, you agree to our <a href="#" class="text-gray-700 underline">Terms and Conditions</a>
                                </div>
                                <div class="flex">
                                    <button type="button" onclick="window.location.href='index.php'" class="bg-white py-3 px-6 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                                        <i class="fas fa-times mr-2"></i> Cancel
                                    </button>
                                    <button type="submit" class="ml-3 inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-700 transition-all duration-200">
                                        <i class="fas fa-paper-plane mr-2"></i> Submit Donation
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-12 bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-xl font-bold text-purple-800 mb-4">Why Donate Food?</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-100 text-purple-600">
                                <i class="fas fa-hand-holding-heart text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-medium text-gray-900">Help Those in Need</h4>
                            <p class="mt-2 text-sm text-gray-500">Your donation can provide a meal for someone who might otherwise go hungry.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-100 text-purple-600">
                                <i class="fas fa-trash-alt text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-medium text-gray-900">Reduce Food Waste</h4>
                            <p class="mt-2 text-sm text-gray-500">Instead of throwing away excess food, share it with those who can use it.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-100 text-purple-600">
                                <i class="fas fa-globe-americas text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-medium text-gray-900">Environmental Impact</h4>
                            <p class="mt-2 text-sm text-gray-500">Reducing food waste helps decrease greenhouse gas emissions and conserve resources.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleFields() {
        const foodType = document.getElementById('food_type').value;
        const quantityField = document.querySelector('.quantity-field');
        const servesField = document.querySelector('.serves-field');
        
        if (foodType === 'raw_food') {
            quantityField.style.display = 'block';
            servesField.style.display = 'none';
            document.getElementById('quantity').setAttribute('required', '');
            document.getElementById('serves_people').removeAttribute('required');
        } else {
            quantityField.style.display = 'none';
            servesField.style.display = 'block';
            document.getElementById('quantity').removeAttribute('required');
            document.getElementById('serves_people').setAttribute('required', '');
        }
    }
    
    // Call toggleFields when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        toggleFields();
    });
    
    // Initialize form fields on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleFields();
        
        // Preview uploaded image
        const fileUpload = document.getElementById('file-upload');
        fileUpload.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const uploadArea = document.querySelector('.border-dashed');
                    uploadArea.innerHTML = `
                        <div class="text-center">
                            <img src="${e.target.result}" alt="Preview" class="mx-auto h-32 w-auto">
                            <p class="mt-2 text-sm text-gray-500">${file.name}</p>
                        </div>
                    `;
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>

<?php
require_once 'includes/footer.php';
?>