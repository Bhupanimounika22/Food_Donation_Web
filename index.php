<?php
require_once 'includes/header.php';

// Check if the database connection is successful
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if the food_donations table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'food_donations'");
if (mysqli_num_rows($table_check) == 0) {
    // Table doesn't exist, create it from database.sql
    $sql_file = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/hack_athon-main/database.sql');
    $sql_array = explode(';', $sql_file);
    
    foreach($sql_array as $sql_query) {
        if(trim($sql_query) != '') {
            mysqli_query($conn, $sql_query);
        }
    }
}

// Get recent donations
$sql = "SELECT fd.*, u.first_name, u.last_name 
        FROM food_donations fd 
        JOIN users u ON fd.donor_id = u.id 
        WHERE fd.status = 'available' 
        ORDER BY fd.created_at DESC 
        LIMIT 6";
$result = $conn->query($sql);

// Debug information
if (!$result) {
    echo display_error("Database query error: " . $conn->error);
}
?>

<!-- Hero Section -->
<section class="relative">
    <div class="slider-container overflow-hidden w-full h-[550px]">
        <div class="slider flex transition-transform duration-500">
            <div class="slide min-w-full relative">
                <img src="https://i.pinimg.com/originals/e8/d8/ee/e8d8ee9edece42916aa5851d7fd7bebd.jpg" alt="Food Donation" class="w-full h-[550px] object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                    <div class="text-center text-white px-4">
                        <h1 class="text-4xl md:text-5xl font-bold mb-4">Share Food, Share Love</h1>
                        <p class="text-xl md:text-2xl mb-8">Join our mission to reduce food waste and feed the hungry</p>
                        <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                            <a href="donate.php" class="btn-primary px-6 py-3 rounded-lg font-bold text-lg">Donate Food</a>
                            <a href="donations.php" class="bg-white text-purple-800 px-6 py-3 rounded-lg font-bold text-lg">Find Food</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slide min-w-full relative">
                <img src="https://i.pinimg.com/736x/3f/73/5e/3f735eb425acbd9397973d4aa0b374d8.jpg" alt="Food Donation" class="w-full h-[550px] object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                    <div class="text-center text-white px-4">
                        <h1 class="text-4xl md:text-5xl font-bold mb-4">No Food Should Go to Waste</h1>
                        <p class="text-xl md:text-2xl mb-8">Help us create a world where everyone has enough to eat</p>
                        <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                            <a href="signup.php" class="btn-primary px-6 py-3 rounded-lg font-bold text-lg">Join Us Today</a>
                            <a href="aboutus.php" class="bg-white text-purple-800 px-6 py-3 rounded-lg font-bold text-lg">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slide min-w-full relative">
                <img src="https://iskcondwarka.org/wp-content/uploads/2022/06/Feeding-the-Poor-Is-this-Charity-worthy.jpg" alt="Food Donation" class="w-full h-[550px] object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                    <div class="text-center text-white px-4">
                        <h1 class="text-4xl md:text-5xl font-bold mb-4">Make a Difference Today</h1>
                        <p class="text-xl md:text-2xl mb-8">Your excess food can save someone from hunger</p>
                        <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                            <a href="donate.php" class="btn-primary px-6 py-3 rounded-lg font-bold text-lg">Start Donating</a>
                            <a href="donations.php" class="bg-white text-purple-800 px-6 py-3 rounded-lg font-bold text-lg">Find Donations</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 primary-color">How It Works</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-purple-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-plus text-3xl primary-color"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Sign Up</h3>
                <p class="text-gray-600">Create an account as a donor or recipient to get started with our platform.</p>
            </div>
            <div class="text-center">
                <div class="bg-purple-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-hand-holding-heart text-3xl primary-color"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Donate or Request</h3>
                <p class="text-gray-600">List your excess food for donation or find available food donations near you.</p>
            </div>
            <div class="text-center">
                <div class="bg-purple-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exchange-alt text-3xl primary-color"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Connect & Share</h3>
                <p class="text-gray-600">Coordinate pickup or delivery and share the joy of giving and receiving.</p>
            </div>
        </div>
    </div>
</section>

<!-- Recent Donations Section -->
<section class="py-16 bg-gray-100">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 primary-color">Recent Donations</h2>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="bg-white rounded-lg overflow-hidden shadow-md card">
                        <div class="h-48 bg-purple-200 flex items-center justify-center">
                            <?php if (!empty($row['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['food_name']); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <i class="fas fa-utensils text-6xl primary-color"></i>
                            <?php endif; ?>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($row['food_name']); ?></h3>
                            <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($row['food_details']); ?></p>
                            <div class="flex items-center mb-2">
                                <i class="fas fa-user mr-2 primary-color"></i>
                                <span><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></span>
                            </div>
                            <div class="flex items-center mb-2">
                                <i class="fas fa-clock mr-2 primary-color"></i>
                                <span>Expires: <?php echo htmlspecialchars($row['expiry_time']); ?></span>
                            </div>
                            <div class="flex items-center mb-4">
                                <i class="fas fa-map-marker-alt mr-2 primary-color"></i>
                                <span><?php echo htmlspecialchars(substr($row['pickup_address'], 0, 50) . (strlen($row['pickup_address']) > 50 ? '...' : '')); ?></span>
                            </div>
                            <a href="donation_details.php?id=<?php echo $row['id']; ?>" class="btn-primary px-4 py-2 rounded-md inline-block">View Details</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="text-center mt-8">
                <a href="donations.php" class="btn-primary px-6 py-3 rounded-lg font-bold inline-block">View All Donations</a>
            </div>
        <?php else: ?>
            <div class="text-center">
                <p class="text-xl mb-8">No donations available at the moment. Be the first to donate!</p>
                <a href="donate.php" class="btn-primary px-6 py-3 rounded-lg font-bold inline-block">Donate Now</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Impact Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 primary-color">Our Impact</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold primary-color mb-2">500+</div>
                <p class="text-gray-600">Food Donations</p>
            </div>
            <div>
                <div class="text-4xl font-bold primary-color mb-2">2,000+</div>
                <p class="text-gray-600">People Fed</p>
            </div>
            <div>
                <div class="text-4xl font-bold primary-color mb-2">100+</div>
                <p class="text-gray-600">Active Donors</p>
            </div>
            <div>
                <div class="text-4xl font-bold primary-color mb-2">50+</div>
                <p class="text-gray-600">Partner Organizations</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 bg-gray-100">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 primary-color">Why Choose Us</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-md card">
                <h3 class="text-xl font-bold mb-4 primary-color">Food Waste Tracking and Prevention</h3>
                <p class="text-gray-600">Monitor and manage food waste in real-time, reducing unnecessary disposal by tracking expiration dates and inventory levels. Implement strategies to prevent waste through efficient utilization and redistribution of surplus food to those in need, fostering a sustainable food ecosystem.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md card">
                <h3 class="text-xl font-bold mb-4 primary-color">Food Banks</h3>
                <p class="text-gray-600">Serve as vital distribution hubs, collecting surplus food donations from restaurants, individuals, and businesses, and redistributing them to disadvantaged communities. Food banks play a pivotal role in alleviating hunger and food insecurity by ensuring surplus food reaches those who need it most.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md card">
                <h3 class="text-xl font-bold mb-4 primary-color">Food Donation by Restaurants/Individuals</h3>
                <p class="text-gray-600">Restaurants and individuals contribute to combating food waste by donating surplus food items rather than discarding them. By participating in donation initiatives, they support local communities and reduce environmental impact while fostering a culture of responsible consumption and generosity.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md card">
                <h3 class="text-xl font-bold mb-4 primary-color">Purchase Surplus Food</h3>
                <p class="text-gray-600">Provide a marketplace for individuals and businesses to purchase surplus food from restaurants and other suppliers at discounted rates. By facilitating the sale of surplus food, this initiative not only prevents waste but also generates revenue that can support food waste reduction efforts and charitable causes.</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-16 primary-bg text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">Join Us Today</h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">Be part of our mission to reduce food waste and fight hunger in our community. Every donation makes a difference.</p>
        <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
            <a href="signup.php" class="bg-white text-purple-800 px-6 py-3 rounded-lg font-bold text-lg">Sign Up Now</a>
            <a href="donate.php" class="border-2 border-white text-white px-6 py-3 rounded-lg font-bold text-lg hover:bg-white hover:text-purple-800 transition-colors">Donate Food</a>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Slider functionality
        const slider = document.querySelector('.slider');
        const slides = document.querySelectorAll('.slide');
        
        if (slider && slides.length > 0) {
            let currentIndex = 0;
            const slideWidth = slides[0].clientWidth;

            function nextSlide() {
                currentIndex = (currentIndex + 1) % slides.length;
                updateSlider();
            }

            function updateSlider() {
                slider.style.transform = `translateX(-${currentIndex * 100}%)`;
            }

            // Auto slide every 5 seconds
            setInterval(nextSlide, 5000);

            // Handle window resize
            window.addEventListener('resize', () => {
                // Update slider position
                updateSlider();
            });
        }
    });
</script>

<?php
require_once 'includes/footer.php';
?>