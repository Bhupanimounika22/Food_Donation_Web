    </main>
    
    <!-- Site Footer -->
    <footer class="site-footer text-white mt-12">
        <!-- Top Footer Section -->
        <div class="border-b border-blue-400">
            <div class="container mx-auto px-4 py-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div>
                        <div class="flex items-center mb-4">
                            <img src="https://cdn-icons-png.flaticon.com/512/3448/3448609.png" class="w-12 h-12 mr-2" alt="Donate Here Logo">
                            <div>
                                <h2 class="text-xl font-bold">DONATE HERE</h2>
                                <p class="text-xs text-blue-100">Food Donation Platform</p>
                            </div>
                        </div>
                        <p class="text-blue-100 mb-6">A platform dedicated to reducing food waste and helping those in need. Together, we can make a difference in our community.</p>
                        <div class="flex space-x-4">
                            <a href="#" class="bg-blue-700 hover:bg-blue-600 h-8 w-8 rounded-sm flex items-center justify-center transition-colors duration-200">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="bg-blue-700 hover:bg-blue-600 h-8 w-8 rounded-sm flex items-center justify-center transition-colors duration-200">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="bg-blue-700 hover:bg-blue-600 h-8 w-8 rounded-sm flex items-center justify-center transition-colors duration-200">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <a href="#" class="bg-blue-700 hover:bg-blue-600 h-8 w-8 rounded-sm flex items-center justify-center transition-colors duration-200">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-bold mb-4 border-b border-blue-400 pb-2">Important Links</h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="index.php" class="text-blue-100 hover:text-white flex items-center transition-colors duration-200">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i> Home
                                </a>
                            </li>
                            <li>
                                <a href="donations.php" class="text-blue-100 hover:text-white flex items-center transition-colors duration-200">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i> Available Donations
                                </a>
                            </li>
                            <li>
                                <a href="donate.php" class="text-blue-100 hover:text-white flex items-center transition-colors duration-200">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i> Donate Food
                                </a>
                            </li>
                            <li>
                                <a href="aboutus.php" class="text-blue-100 hover:text-white flex items-center transition-colors duration-200">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i> About Us
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-blue-100 hover:text-white flex items-center transition-colors duration-200">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i> FAQ
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-blue-100 hover:text-white flex items-center transition-colors duration-200">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i> Terms & Conditions
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-blue-100 hover:text-white flex items-center transition-colors duration-200">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i> Privacy Policy
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-bold mb-4 border-b border-blue-400 pb-2">Food Categories</h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="donations.php?food_type=raw_food" class="text-blue-100 hover:text-white flex items-center transition-colors duration-200">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i> Raw Food
                                </a>
                            </li>
                            <li>
                                <a href="donations.php?food_type=cooked_food" class="text-blue-100 hover:text-white flex items-center transition-colors duration-200">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i> Cooked Food
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-blue-100 hover:text-white flex items-center transition-colors duration-200">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i> Packaged Food
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-blue-100 hover:text-white flex items-center transition-colors duration-200">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i> Fruits & Vegetables
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-blue-100 hover:text-white flex items-center transition-colors duration-200">
                                    <i class="fas fa-chevron-right text-xs mr-2"></i> Grains & Cereals
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-bold mb-4 border-b border-blue-400 pb-2">Contact Us</h3>
                        <ul class="space-y-3">
                            <li class="flex items-center text-blue-100">
                                <i class="fas fa-envelope w-5 mr-3 text-blue-200"></i>
                                <a href="mailto:<?php echo $site_email; ?>" class="hover:text-white transition-colors duration-200"><?php echo $site_email; ?></a>
                            </li>
                            <li class="flex items-center text-blue-100">
                                <i class="fas fa-phone w-5 mr-3 text-blue-200"></i>
                                <a href="tel:+919247000002" class="hover:text-white transition-colors duration-200">+91 92470 00002</a>
                            </li>
                            <li class="flex items-center text-blue-100">
                                <i class="fas fa-phone w-5 mr-3 text-blue-200"></i>
                                <a href="tel:1800114000" class="hover:text-white transition-colors duration-200">Toll Free: 1800-11-4000</a>
                            </li>
                            <li class="flex items-start text-blue-100">
                                <i class="fas fa-map-marker-alt w-5 mr-3 mt-1 text-blue-200"></i>
                                <span>Donate Here Headquarters, 123 Main Street, Anytown, India - 110001</span>
                            </li>
                            <li class="flex items-center text-blue-100 mt-4">
                                <i class="fas fa-clock w-5 mr-3 text-blue-200"></i>
                                <span>Working Hours: 9:00 AM - 5:30 PM (Mon-Fri)</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom Footer Section -->
        <div class="bg-blue-800 py-3">
            <div class="container mx-auto px-4">
                <div class="text-center">
                    <p class="text-xs text-blue-100">&copy; <?php echo date('Y'); ?> <?php echo $site_name; ?>. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    </script>
</body>
</html>