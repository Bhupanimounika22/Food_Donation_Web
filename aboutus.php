<?php
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-purple-900 to-purple-700 text-white py-20">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">About Donate Here</h1>
            <p class="text-xl mb-8">From Waste to Taste: Strategies for Reducing Food Waste and Maximizing Utilization</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="donate.php" class="bg-white text-purple-800 px-6 py-3 rounded-lg font-bold text-lg hover:bg-gray-100 transition-colors">Donate Food</a>
                <a href="signup.php" class="border-2 border-white text-white px-6 py-3 rounded-lg font-bold text-lg hover:bg-white hover:text-purple-800 transition-colors">Join Us</a>
            </div>
        </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full h-auto">
            <path fill="#ffffff" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,149.3C960,160,1056,160,1152,138.7C1248,117,1344,75,1392,53.3L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
</section>

<!-- Our Mission Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-6 primary-color">Our Mission</h2>
            <p class="text-lg text-gray-700">At Donate Here, we're committed to reducing food waste and fighting hunger in our communities. We connect food donors with those in need, creating a more sustainable and equitable food system for everyone.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-purple-50 rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow">
                <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-utensils text-3xl primary-color"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-center primary-color">Reduce Food Waste</h3>
                <p class="text-gray-700 text-center">We help prevent perfectly good food from going to waste by connecting donors with recipients in real-time.</p>
            </div>
            
            <div class="bg-purple-50 rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow">
                <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-hands-helping text-3xl primary-color"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-center primary-color">Fight Hunger</h3>
                <p class="text-gray-700 text-center">We believe everyone deserves access to nutritious food, and we're working to make that a reality in communities everywhere.</p>
            </div>
            
            <div class="bg-purple-50 rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow">
                <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-globe-americas text-3xl primary-color"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-center primary-color">Environmental Impact</h3>
                <p class="text-gray-700 text-center">By reducing food waste, we're also reducing greenhouse gas emissions and conserving the resources used to produce food.</p>
            </div>
        </div>
    </div>
</section>

<!-- The Problem Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-6 primary-color">Understanding Food Waste</h2>
            <p class="text-lg text-gray-700">Food waste is a significant global issue with profound environmental, economic, and social implications.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <div class="h-48 bg-gradient-to-r from-green-500 to-green-700 flex items-center justify-center">
                    <i class="fas fa-tree text-6xl text-white"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-3 text-green-700">Environmental Impacts</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Food waste in landfills produces methane, a potent greenhouse gas</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Wasted food means wasted land, water, energy, and labor</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                            <span>Agricultural expansion leads to deforestation and biodiversity loss</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <div class="h-48 bg-gradient-to-r from-blue-500 to-blue-700 flex items-center justify-center">
                    <i class="fas fa-chart-line text-6xl text-white"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-3 text-blue-700">Economic Impacts</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-2"></i>
                            <span>Global economic cost of food waste exceeds $1 trillion annually</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-2"></i>
                            <span>Businesses lose money on unsold or discarded products</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-2"></i>
                            <span>Municipalities bear costs of food waste collection and disposal</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <div class="h-48 bg-gradient-to-r from-red-500 to-red-700 flex items-center justify-center">
                    <i class="fas fa-users text-6xl text-white"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-3 text-red-700">Social Impacts</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-red-500 mt-1 mr-2"></i>
                            <span>Millions go hungry while food is wasted elsewhere</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-red-500 mt-1 mr-2"></i>
                            <span>Ethical concerns about resource allocation and distribution</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-red-500 mt-1 mr-2"></i>
                            <span>Cultural norms around food abundance contribute to waste</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Solution Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-6 primary-color">Our Solution</h2>
            <p class="text-lg text-gray-700">Donate Here provides a comprehensive platform to connect food donors with recipients, making it easy to reduce waste and help those in need.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <img src="https://images.unsplash.com/photo-1593113598332-cd59a93c5156?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Food Donation" class="rounded-xl shadow-lg">
            </div>
            
            <div class="space-y-6">
                <div class="flex items-start">
                    <div class="bg-purple-100 rounded-full w-10 h-10 flex items-center justify-center mr-4 mt-1 flex-shrink-0">
                        <i class="fas fa-mobile-alt text-purple-700"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-2 primary-color">Easy-to-Use Platform</h3>
                        <p class="text-gray-700">Our user-friendly website makes it simple for donors to list available food and for recipients to find donations nearby.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="bg-purple-100 rounded-full w-10 h-10 flex items-center justify-center mr-4 mt-1 flex-shrink-0">
                        <i class="fas fa-map-marker-alt text-purple-700"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-2 primary-color">Local Connections</h3>
                        <p class="text-gray-700">We focus on connecting donors and recipients in the same community, reducing transportation emissions and ensuring food freshness.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="bg-purple-100 rounded-full w-10 h-10 flex items-center justify-center mr-4 mt-1 flex-shrink-0">
                        <i class="fas fa-shield-alt text-purple-700"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-2 primary-color">Safety First</h3>
                        <p class="text-gray-700">We provide guidelines for safe food handling and storage to ensure all donated food is safe for consumption.</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="bg-purple-100 rounded-full w-10 h-10 flex items-center justify-center mr-4 mt-1 flex-shrink-0">
                        <i class="fas fa-heart text-purple-700"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-2 primary-color">Community Building</h3>
                        <p class="text-gray-700">Beyond food donation, we're creating a community of like-minded individuals committed to reducing waste and helping others.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-6 primary-color">Our Team</h2>
            <p class="text-lg text-gray-700">Meet the passionate individuals behind Donate Here who are dedicated to making a difference in our communities.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow text-center">
                <div class="p-6">
                    <div class="w-32 h-32 rounded-full overflow-hidden mx-auto mb-4">
                        <img src="https://media.istockphoto.com/id/1255163297/vector/user-profile-icon-vector-avatar-portrait-symbol-flat-shape-person-sign-logo-black-silhouette.jpg?s=612x612&w=0&k=20&c=p6azyhUBIcWx6-aXVRPUTveaVqbTA2bNXpBoGQjEB68=" alt="Team Member" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold mb-1 primary-color">Bhupani Mounika</h3>
                    <p class="text-gray-600 mb-3">Founder & CEO</p>
                    <p class="text-gray-700 mb-4">Passionate about sustainable food systems and community building.</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-400 hover:text-blue-500"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-gray-400 hover:text-blue-400"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-purple-700"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow text-center">
                <div class="p-6">
                    <div class="w-32 h-32 rounded-full overflow-hidden mx-auto mb-4">
                        <img src="https://media.istockphoto.com/id/1255163297/vector/user-profile-icon-vector-avatar-portrait-symbol-flat-shape-person-sign-logo-black-silhouette.jpg?s=612x612&w=0&k=20&c=p6azyhUBIcWx6-aXVRPUTveaVqbTA2bNXpBoGQjEB68=" alt="Team Member" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold mb-1 primary-color">Laxmisai</h3>
                    <p class="text-gray-600 mb-3">Developer</p>
                    <p class="text-gray-700 mb-4">Tech expert with a background in sustainable development and social impact.</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-400 hover:text-blue-500"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-gray-400 hover:text-blue-400"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-purple-700"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow text-center">
                <div class="p-6">
                    <div class="w-32 h-32 rounded-full overflow-hidden mx-auto mb-4">
                        <img src="https://media.istockphoto.com/id/1255163297/vector/user-profile-icon-vector-avatar-portrait-symbol-flat-shape-person-sign-logo-black-silhouette.jpg?s=612x612&w=0&k=20&c=p6azyhUBIcWx6-aXVRPUTveaVqbTA2bNXpBoGQjEB68=" alt="Team Member" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold mb-1 primary-color">Mohana</h3>
                    <p class="text-gray-600 mb-3">Developer</p>
                    <p class="text-gray-700 mb-4">Expert in logistics and supply chain management with a focus on food systems.</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-400 hover:text-blue-500"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-gray-400 hover:text-blue-400"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-purple-700"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow text-center">
                <div class="p-6">
                    <div class="w-32 h-32 rounded-full overflow-hidden mx-auto mb-4">
                        <img src="https://media.istockphoto.com/id/1255163297/vector/user-profile-icon-vector-avatar-portrait-symbol-flat-shape-person-sign-logo-black-silhouette.jpg?s=612x612&w=0&k=20&c=p6azyhUBIcWx6-aXVRPUTveaVqbTA2bNXpBoGQjEB68=" alt="Team Member" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold mb-1 primary-color">Toufiq</h3>
                    <p class="text-gray-600 mb-3">Developer</p>
                    <p class="text-gray-700 mb-4">Dedicated to building relationships with donors, recipients, and community partners.</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-400 hover:text-blue-500"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="text-gray-400 hover:text-blue-400"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-purple-700"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Join Us Section -->
<section class="py-16 bg-gradient-to-r from-purple-900 to-purple-700 text-white">
    <div class="container mx-auto px-4 text-center">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Join Our Mission Today</h2>
            <p class="text-xl mb-8">Whether you want to donate food, receive donations, or volunteer your time, there's a place for you in our community.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="signup.php" class="bg-white text-purple-800 px-6 py-3 rounded-lg font-bold text-lg hover:bg-gray-100 transition-colors">Sign Up Now</a>
                <a href="donate.php" class="border-2 border-white text-white px-6 py-3 rounded-lg font-bold text-lg hover:bg-white hover:text-purple-800 transition-colors">Donate Food</a>
            </div>
            <div class="mt-12 flex justify-center space-x-6">
                <a href="#" class="text-white hover:text-purple-200 transition-colors">
                    <i class="fab fa-facebook-f text-2xl"></i>
                </a>
                <a href="#" class="text-white hover:text-purple-200 transition-colors">
                    <i class="fab fa-twitter text-2xl"></i>
                </a>
                <a href="#" class="text-white hover:text-purple-200 transition-colors">
                    <i class="fab fa-instagram text-2xl"></i>
                </a>
                <a href="#" class="text-white hover:text-purple-200 transition-colors">
                    <i class="fab fa-linkedin-in text-2xl"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>