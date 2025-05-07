<?php
require_once 'includes/header.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get filter parameters
$food_type = isset($_GET['food_type']) ? sanitize_input($_GET['food_type']) : '';
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Build base query with prepared statement
$sql = "SELECT fd.*, u.first_name, u.last_name 
        FROM food_donations fd 
        JOIN users u ON fd.donor_id = u.id 
        WHERE fd.status = 'available'";
$params = array();
$types = "";

// Add filters if provided
if (!empty($food_type)) {
    $sql .= " AND fd.food_type = ?";
    $params[] = $food_type;
    $types .= "s";
}

if (!empty($search)) {
    $sql .= " AND (fd.food_name LIKE ? OR fd.food_details LIKE ? OR fd.pickup_address LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

$sql .= " ORDER BY fd.created_at DESC";

// Prepare and execute the query
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) {
    die("Error executing query: " . $stmt->error);
}

$result = $stmt->get_result();

// Debug information
error_log("Query executed successfully. Found " . $result->num_rows . " donations");
?>

<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="inline-block mb-4">
                <div class="flex items-center justify-center w-16 h-16 mx-auto bg-blue-100 rounded-full">
                    <img src="https://cdn-icons-png.flaticon.com/512/3448/3448609.png" class="w-10 h-10" alt="Donate Here Logo">
                </div>
            </div>
            <h2 class="text-3xl font-bold text-blue-900 sm:text-4xl">
                Available Food Donations
            </h2>
            <div class="w-24 h-1 mx-auto mt-4 mb-6 bg-blue-500"></div>
            <p class="mt-4 text-lg text-gray-600">
                Browse all available food donations and find what you need
            </p>
        </div>
        
        <!-- Search and Filter Section -->
        <div class="mt-8 bg-white p-6 shadow-md border border-blue-100 rounded-lg">
            <div class="border-b border-blue-100 pb-4 mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-filter mr-2 text-blue-500"></i> Search & Filter Donations
                </h3>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700">Search Keywords</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-500"></i>
                        </div>
                        <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by food name, details, or location" class="pl-10 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md transition-all duration-200">
                    </div>
                </div>
                
                <div class="flex-1">
                    <label for="food_type" class="block text-sm font-medium text-gray-700">Food Type</label>
                    <select id="food_type" name="food_type" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Types</option>
                        <option value="raw_food" <?php echo $food_type === 'raw_food' ? 'selected' : ''; ?>>Raw Food</option>
                        <option value="cooked_food" <?php echo $food_type === 'cooked_food' ? 'selected' : ''; ?>>Cooked Food</option>
                    </select>
                </div>
                
                <div class="flex space-x-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <i class="fas fa-search mr-2"></i>
                        Search
                    </button>
                    
                    <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Donations Grid -->
        <div class="mt-8">
            <?php if ($result && $result->num_rows > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php while ($donation = $result->fetch_assoc()): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-blue-100 hover:shadow-lg transition-shadow duration-300">
                            <div class="bg-blue-50 px-6 py-4 border-b border-blue-100">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-xl font-bold text-blue-900 truncate">
                                        <a href="donation_details.php?id=<?php echo $donation['id']; ?>" class="hover:text-blue-700 transition-colors duration-200">
                                            <?php echo htmlspecialchars($donation['food_name']); ?>
                                        </a>
                                    </h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $donation['food_type'] == 'raw_food' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>">
                                        <?php echo $donation['food_type'] == 'raw_food' ? 'Raw Food' : 'Cooked Food'; ?>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-user mr-1"></i> Donated by <?php echo htmlspecialchars($donation['first_name'] . ' ' . substr($donation['last_name'], 0, 1) . '.'); ?>
                                </p>
                            </div>
                            
                            <div class="p-6">
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                    <?php echo htmlspecialchars($donation['food_details']); ?>
                                </p>
                                
                                <div class="space-y-2">
                                    <?php if ($donation['food_type'] == 'raw_food' && !empty($donation['quantity'])): ?>
                                        <div class="flex items-center text-sm text-gray-500">
                                            <i class="fas fa-weight-hanging mr-2 text-blue-500"></i>
                                            <span>Quantity: <?php echo htmlspecialchars($donation['quantity']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($donation['food_type'] == 'cooked_food' && !empty($donation['serves_people'])): ?>
                                        <div class="flex items-center text-sm text-gray-500">
                                            <i class="fas fa-users mr-2 text-blue-500"></i>
                                            <span>Serves: <?php echo htmlspecialchars($donation['serves_people']); ?> people</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-clock mr-2 text-blue-500"></i>
                                        <span>Expiry: <?php echo htmlspecialchars($donation['expiry_time']); ?></span>
                                    </div>
                                    
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
                                        <span class="line-clamp-1"><?php echo htmlspecialchars($donation['pickup_address']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-blue-100 flex space-x-2">
                                    <a href="donation_details.php?id=<?php echo $donation['id']; ?>" class="inline-flex items-center justify-center flex-1 px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        View Details
                                    </a>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="donation_details.php?id=<?php echo $donation['id']; ?>#reserve" class="inline-flex items-center justify-center flex-1 px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                        <i class="fas fa-hand-holding-heart mr-2"></i>
                                        Reserve
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-16 bg-white shadow-md border border-blue-100 rounded-lg">
                    <div class="bg-blue-100 w-20 h-20 flex items-center justify-center mx-auto mb-6 rounded-full">
                        <i class="fas fa-search text-4xl text-blue-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-blue-900 mb-2">No Donations Found</h3>
                    <div class="w-16 h-1 mx-auto mt-2 mb-4 bg-blue-500"></div>
                    <p class="text-gray-600 max-w-md mx-auto">
                        <?php if (!empty($search) || !empty($food_type)): ?>
                            No donations match your search criteria. Please try adjusting your filters or check back later.
                        <?php else: ?>
                            There are no available food donations in the database at the moment. Be the first to contribute!
                        <?php endif; ?>
                    </p>
                    <div class="mt-8">
                        <a href="donate.php" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <i class="fas fa-plus-circle mr-2"></i> Submit New Donation
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Call to Action -->
        <div class="mt-16 bg-blue-600 shadow-lg overflow-hidden border border-blue-500 rounded-lg">
            <div class="px-6 py-12 sm:px-12 lg:py-16 lg:pr-0 xl:py-16 xl:px-20">
                <div class="lg:flex lg:items-center lg:justify-between">
                    <div class="lg:w-0 lg:flex-1">
                        <div class="flex items-center mb-4">
                            <img src="https://cdn-icons-png.flaticon.com/512/3448/3448609.png" class="w-12 h-12 mr-4" alt="Donate Here Logo">
                            <h2 class="text-2xl font-bold text-white">
                                Join Our Food Donation Initiative
                            </h2>
                        </div>
                        <p class="mt-4 text-lg leading-6 text-blue-100">
                            Help us reduce food waste and support those in need. Your contribution can make a significant difference in someone's life.
                        </p>
                        <div class="mt-6 flex flex-col sm:flex-row sm:space-x-4">
                            <a href="donate.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-blue-800 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-600 focus:ring-white transition-all duration-200 mb-3 sm:mb-0">
                                <i class="fas fa-plus-circle mr-2"></i> Donate Food
                            </a>
                            <a href="aboutus.php" class="inline-flex items-center px-6 py-3 border border-blue-300 text-base font-medium rounded-md shadow-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-600 focus:ring-white transition-all duration-200">
                                <i class="fas fa-info-circle mr-2"></i> Learn More
                            </a>
                        </div>
                    </div>
                    <div class="mt-8 lg:mt-0 lg:ml-8 hidden lg:block">
                        <div class="flex items-center justify-center bg-blue-700 p-6 rounded-lg border border-blue-500">
                            <div class="text-center">
                                <div class="text-4xl font-bold text-white mb-2">
                                    <i class="fas fa-phone-alt mr-2 text-blue-300"></i> 1800-11-4000
                                </div>
                                <p class="text-blue-200">Toll Free Helpline</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<?php
$stmt->close();
require_once 'includes/footer.php';
?>