<?php
require_once 'includes/header.php';

// Get filter parameters
$food_type = isset($_GET['food_type']) ? sanitize_input($_GET['food_type']) : '';
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

// Build query
$sql = "SELECT fd.*, u.first_name, u.last_name 
        FROM food_donations fd 
        JOIN users u ON fd.donor_id = u.id 
        WHERE fd.status = 'available'";

// Debug information
error_log("SQL Query: " . $sql);

// Add filters if provided
if (!empty($food_type)) {
    $sql .= " AND fd.food_type = '$food_type'";
}

if (!empty($search)) {
    $sql .= " AND (fd.food_name LIKE '%$search%' OR fd.food_details LIKE '%$search%' OR fd.pickup_address LIKE '%$search%')";
}

$sql .= " ORDER BY fd.created_at DESC";

$result = $conn->query($sql);
error_log("Query result: " . ($result ? "Success, found " . $result->num_rows . " rows" : "Failed: " . $conn->error));
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
                Available Food Donations
            </h2>
            <div class="w-24 h-1 mx-auto mt-4 mb-6 bg-gray-800"></div>
            <p class="mt-4 text-lg text-gray-600">
                Official directory of available food donations under the Ministry of Food Distribution
            </p>
        </div>
        
        <!-- Search and Filter Section -->
        <div class="mt-8 bg-white p-6 shadow-md border border-gray-300">
            <div class="border-b border-gray-300 pb-4 mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-filter mr-2 text-gray-600"></i> Search & Filter Donations
                </h3>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700">Search Keywords</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-500"></i>
                        </div>
                        <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by food name, details, or location" class="pl-10 py-2 shadow-sm focus:ring-gray-500 focus:border-gray-500 block w-full sm:text-sm border-gray-300 rounded-md transition-all duration-200">
                    </div>
                </div>
                
                <div class="w-full md:w-1/4">
                    <label for="food_type" class="block text-sm font-medium text-gray-700">Food Category</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-utensils text-gray-500"></i>
                        </div>
                        <select id="food_type" name="food_type" class="pl-10 py-2 shadow-sm focus:ring-gray-500 focus:border-gray-500 block w-full sm:text-sm border-gray-300 rounded-md transition-all duration-200">
                            <option value="">All Categories</option>
                            <option value="raw_food" <?php echo $food_type == 'raw_food' ? 'selected' : ''; ?>>Raw Food</option>
                            <option value="cooked_food" <?php echo $food_type == 'cooked_food' ? 'selected' : ''; ?>>Cooked Food</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex space-x-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-700 transition-all duration-200">
                        <i class="fas fa-search mr-2"></i>
                        Search
                    </button>
                    
                    <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Donations Grid -->
        <div class="mt-12">
            <?php if ($result && $result->num_rows > 0): ?>
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="bg-white overflow-hidden shadow-md border border-gray-300 transition-all duration-300 hover:shadow-lg">
                            <div class="h-48 bg-gray-100 flex items-center justify-center overflow-hidden relative">
                                <?php if (!empty($row['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['food_name']); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <?php if ($row['food_type'] == 'raw_food'): ?>
                                        <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Raw Food" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Cooked Food" class="w-full h-full object-cover">
                                    <?php endif; ?>
                                <?php endif; ?>
                                <div class="absolute top-0 left-0 w-full bg-gray-800 bg-opacity-70 text-white text-xs py-1 px-2 flex justify-between items-center">
                                    <span class="font-bold">REF: DON-<?php echo $row['id']; ?></span>
                                    <span class="<?php echo $row['food_type'] == 'raw_food' ? 'bg-green-700' : 'bg-blue-700'; ?> px-2 py-1 rounded-sm">
                                        <?php echo $row['food_type'] == 'raw_food' ? 'Raw Food' : 'Cooked Food'; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <div class="border-b border-gray-200 pb-2 mb-3">
                                    <h3 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($row['food_name']); ?></h3>
                                </div>
                                
                                <p class="text-sm text-gray-600 line-clamp-2 mb-4"><?php echo htmlspecialchars($row['food_details']); ?></p>
                                
                                <div class="space-y-2 text-xs">
                                    <div class="flex items-center text-gray-600 bg-gray-50 p-2 rounded">
                                        <i class="fas fa-user text-gray-700 mr-3 w-5 text-center"></i>
                                        <span><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></span>
                                    </div>
                                    
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-clock text-gray-700 mr-3 w-5 text-center"></i>
                                        <span>Expires: <?php echo htmlspecialchars($row['expiry_time']); ?></span>
                                    </div>
                                    
                                    <?php if ($row['food_type'] == 'raw_food' && !empty($row['quantity'])): ?>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-weight text-gray-700 mr-3 w-5 text-center"></i>
                                            <span>Quantity: <?php echo htmlspecialchars($row['quantity']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($row['food_type'] == 'cooked_food' && !empty($row['serves_people'])): ?>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-users text-gray-700 mr-3 w-5 text-center"></i>
                                            <span>Serves: <?php echo htmlspecialchars($row['serves_people']); ?> people</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="flex items-start text-gray-600">
                                        <i class="fas fa-map-marker-alt text-gray-700 mr-3 w-5 text-center mt-1"></i>
                                        <span class="truncate"><?php echo htmlspecialchars(substr($row['pickup_address'], 0, 50) . (strlen($row['pickup_address']) > 50 ? '...' : '')); ?></span>
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-3 border-t border-gray-200 flex justify-between items-center">
                                    <span class="text-xs text-gray-500">Posted: <?php echo date('d M Y', strtotime($row['created_at'])); ?></span>
                                    <a href="donation_details.php?id=<?php echo $row['id']; ?>" class="flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-700 transition-all duration-200">
                                        <i class="fas fa-info-circle mr-2"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-16 bg-white shadow-md border border-gray-300">
                    <div class="bg-gray-200 w-20 h-20 flex items-center justify-center mx-auto mb-6 rounded-full">
                        <i class="fas fa-search text-4xl text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2 uppercase">No Records Found</h3>
                    <div class="w-16 h-1 mx-auto mt-2 mb-4 bg-gray-800"></div>
                    <p class="text-gray-600 max-w-md mx-auto">
                        <?php if (!empty($search) || !empty($food_type)): ?>
                            No donations match your search criteria. Please try adjusting your filters or check back later.
                        <?php else: ?>
                            There are no available food donations in the database at the moment. Be the first to contribute!
                        <?php endif; ?>
                    </p>
                    <div class="mt-8">
                        <a href="donate.php" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-700 transition-all duration-200">
                            <i class="fas fa-plus-circle mr-2"></i> Submit New Donation
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Call to Action -->
        <div class="mt-16 bg-gray-800 shadow-lg overflow-hidden border border-gray-700">
            <div class="px-6 py-12 sm:px-12 lg:py-16 lg:pr-0 xl:py-16 xl:px-20">
                <div class="lg:flex lg:items-center lg:justify-between">
                    <div class="lg:w-0 lg:flex-1">
                        <div class="flex items-center mb-4">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/Emblem_of_India.svg" class="w-12 h-12 mr-4" alt="Government Emblem">
                            <h2 class="text-2xl font-bold text-white uppercase">
                                Official Food Donation Initiative
                            </h2>
                        </div>
                        <p class="mt-4 text-lg leading-6 text-gray-300">
                            The Ministry of Food Distribution invites all citizens to participate in this national initiative to reduce food waste and support those in need.
                        </p>
                        <div class="mt-6 flex flex-col sm:flex-row sm:space-x-4">
                            <a href="donate.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-gray-800 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white transition-all duration-200 mb-3 sm:mb-0">
                                <i class="fas fa-plus-circle mr-2"></i> Register Donation
                            </a>
                            <a href="aboutus.php" class="inline-flex items-center px-6 py-3 border border-gray-500 text-base font-medium rounded-md shadow-sm text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white transition-all duration-200">
                                <i class="fas fa-info-circle mr-2"></i> Program Details
                            </a>
                        </div>
                    </div>
                    <div class="mt-8 lg:mt-0 lg:ml-8 hidden lg:block">
                        <div class="flex items-center justify-center bg-gray-700 p-6 rounded-lg border border-gray-600">
                            <div class="text-center">
                                <div class="text-4xl font-bold text-white mb-2">
                                    <i class="fas fa-phone-alt mr-2 text-gray-400"></i> 1800-11-4000
                                </div>
                                <p class="text-gray-400">Toll Free Helpline</p>
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
</style>

<?php
require_once 'includes/footer.php';
?>