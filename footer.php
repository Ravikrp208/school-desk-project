    </main>
    <!-- Footer -->
    <footer class="bg-[#0F172A] pt-20 pb-12 relative overflow-hidden text-white">
        <!-- Colorful Top Border -->
        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-blue-600 via-indigo-500 to-orange-400"></div>
        
        <!-- Subtle Glow Overlay -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500/10 blur-[150px] rounded-full -translate-y-1/2 translate-x-1/2"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <!-- Brand Column -->
                <div class="col-span-1">
                    <a href="<?php echo $base_url; ?>/" class="flex items-center mb-8 group">
                        <div class="h-16 w-16 bg-white rounded-2xl flex items-center justify-center p-1 shadow-2xl border-4 border-white overflow-hidden transform group-hover:scale-110 transition-transform duration-500">
                            <img src="<?php echo $base_url; ?>/assets/images/logo_boy.png" alt="Logo" class="w-full h-full object-contain">
                        </div>
                    </a>
                    <p class="text-slate-400 text-sm font-medium leading-relaxed mb-10 opacity-80">
                        The Digital Atelier of Discovery. Helping parents navigate the complex world of education since 2026.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all"><i class="fa-solid fa-globe"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all"><i class="fa-solid fa-share-nodes"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all"><i class="fa-solid fa-envelope"></i></a>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="col-span-1">
                    <h4 class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-8">NAVIGATION</h4>
                    <ul class="space-y-4">
                        <li><a href="<?php echo $base_url; ?>/about.php" class="text-slate-300 font-bold text-sm hover:text-white transition-colors">About Us</a></li>
                        <li><a href="<?php echo $base_url; ?>/terms.php" class="text-slate-300 font-bold text-sm hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="<?php echo $base_url; ?>/privacy.php" class="text-slate-300 font-bold text-sm hover:text-white transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div class="col-span-1">
                    <h4 class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-8">SUPPORT</h4>
                    <ul class="space-y-4">
                        <li><a href="<?php echo $base_url; ?>/contact.php" class="text-slate-300 font-bold text-sm hover:text-white transition-colors">Contact Support</a></li>
                        <li><a href="<?php echo $base_url; ?>/school_dashboard" class="text-slate-300 font-bold text-sm hover:text-white transition-colors">School Login</a></li>
                    </ul>
                </div>

                <!-- Subscribe -->
                <div class="col-span-1">
                    <h4 class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-8">SUBSCRIBE</h4>
                    <p class="text-slate-400 text-sm font-medium mb-8 opacity-80">Get the latest education news and admission updates.</p>
                    <div class="relative group">
                        <input type="email" placeholder="Email address" class="w-full bg-white/5 border border-white/10 rounded-xl py-5 px-6 pr-16 text-sm font-medium text-white focus:bg-white/10 focus:border-blue-500 transition-all outline-none backdrop-blur-md">
                        <button class="absolute right-2 top-2 bottom-2 bg-blue-600 text-white w-12 rounded-lg flex items-center justify-center hover:bg-orange-500 transition-all shadow-xl shadow-blue-500/20 active:scale-95">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-between pt-12 border-t border-white/10 gap-6">
                <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest">&copy; 2026 MySchoolDesk. The Digital Atelier of Discovery.</p>
                <div class="flex items-center gap-8">
                    <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest">Privacy Policy</p>
                    <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest">Made with Trust in Vadodara</p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Floating Concierge FAB -->
    <div class="fixed bottom-8 right-8 z-[100] group hidden md:block">
        <div class="absolute -top-12 right-0 bg-slate-900 text-white text-[10px] font-black px-4 py-2 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap shadow-2xl">
            ADMISSION ASSISTANCE <i class="fa-solid fa-caret-down absolute -bottom-2 right-6 text-slate-900"></i>
        </div>
        <button class="w-16 h-16 bg-[#1D4ED8] text-white rounded-2xl shadow-[0_15px_35px_-10px_rgba(29,78,216,0.6)] flex items-center justify-center hover:scale-110 hover:rotate-12 transition-all duration-300 relative">
            <i class="fa-solid fa-comment-dots text-2xl"></i>
            <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 border-2 border-white rounded-full"></span>
        </button>
    </div>
    
    <script src="<?php echo $base_url; ?>/assets/js/script.js"></script>
</body>
</html>
