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
                    <div class="flex flex-col gap-4">
                        <a href="mailto:help@myschooldesk.co.in" class="flex items-center gap-3 group/mail text-slate-400 hover:text-white transition-colors">
                            <i class="fa-solid fa-envelope w-5 text-blue-500"></i>
                            <span class="text-xs font-bold">help@myschooldesk.co.in</span>
                        </a>
                        <a href="tel:+916351165654" class="flex items-center gap-3 group/phone text-slate-400 hover:text-white transition-colors">
                            <i class="fa-solid fa-phone w-5 text-orange-400"></i>
                            <span class="text-xs font-bold">+91 63511 65654</span>
                        </a>
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
    
    <!-- Global Parent Enquiry Modal -->
    <div id="parentEnquiryModal" class="fixed inset-0 z-[5000] hidden overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-xl" onclick="closeEnquiryModal()"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative w-full max-w-4xl bg-white rounded-[48px] shadow-2xl overflow-hidden animate-fade-in-up flex flex-col md:flex-row border border-white/20">
                <!-- Close Button -->
                <button onclick="closeEnquiryModal()" class="absolute top-8 right-8 z-50 w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-slate-400 hover:text-slate-900 shadow-xl transition-all active:scale-95">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>

                <!-- Left Panel: Branding & Trust -->
                <div class="md:w-5/12 bg-[#0F172A] p-12 md:p-16 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 blur-[80px] rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    
                    <div class="relative z-10 h-full flex flex-col">
                        <div class="mb-12">
                            <span class="inline-block bg-blue-600 px-3 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest mb-4">Express Enquiry</span>
                            <h2 class="text-3xl font-black leading-tight mb-4">Start Your Child's <span class="text-blue-500">Bright Journey</span></h2>
                            <p class="text-slate-400 font-medium text-sm">Fill out this quick form. Our senior consultant will call you to schedule a visit and assist with documents for all selected schools.</p>
                        </div>

                        <div class="space-y-6 mt-auto">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-blue-500 border border-white/10"><i class="fa-solid fa-phone"></i></div>
                                <div>
                                    <p class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Callback Within</p>
                                    <p class="font-black">24-48 Hours</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-orange-500 border border-white/10"><i class="fa-solid fa-calendar-check"></i></div>
                                <div>
                                    <p class="text-[8px] font-black text-slate-500 uppercase tracking-widest">Includes</p>
                                    <p class="font-black">Free School Visits</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: The Form -->
                <div class="md:w-7/12 p-12 md:p-16 bg-white overflow-y-auto max-h-[90vh]">
                    <div class="mb-10">
                        <h4 class="text-sm font-black text-slate-400 uppercase tracking-[0.2em] mb-2">TARGET SCHOOLS</h4>
                        <div id="enquirySchoolBadges" class="flex flex-wrap gap-2">
                            <p id="enquirySchoolName" class="text-2xl font-black text-blue-600 truncate">Select a School</p>
                        </div>
                    </div>

                    <form action="enquiry_submit.php" method="POST" class="space-y-6">
                        <div id="enquirySchoolIdContainer">
                            <input type="hidden" name="school_ids[]" id="enquirySchoolId">
                        </div>
                        
                        <div class="space-y-4">
                            <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 pb-2 mb-4">PARENT INFORMATION</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="relative group">
                                    <label class="absolute left-6 top-3 text-[10px] font-black text-blue-600 uppercase tracking-widest transition-all">Parent Name</label>
                                    <input type="text" name="parent_name" required class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-3xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all" placeholder="John Doe">
                                </div>
                                <div class="relative group">
                                    <label class="absolute left-6 top-3 text-[10px] font-black text-blue-600 uppercase tracking-widest transition-all">Mobile Number</label>
                                    <input type="tel" name="mobile" required class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-3xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all" placeholder="9999999999">
                                </div>
                            </div>
                            <div class="relative group">
                                <label class="absolute left-6 top-3 text-[10px] font-black text-blue-600 uppercase tracking-widest transition-all">Email Address</label>
                                <input type="email" name="email" required class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-3xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all" placeholder="john@example.com">
                            </div>
                        </div>

                        <div class="space-y-4 pt-4">
                            <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 pb-2 mb-4">ADMISSION DETAILS</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="relative group">
                                    <label class="absolute left-6 top-3 text-[10px] font-black text-blue-600 uppercase tracking-widest transition-all">Child Name</label>
                                    <input type="text" name="child_name" required class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-3xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all" placeholder="Child's Name">
                                </div>
                                <div class="relative group">
                                    <label class="absolute left-6 top-3 text-[10px] font-black text-blue-600 uppercase tracking-widest transition-all">Standard</label>
                                    <select name="child_class" required class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-3xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all appearance-none cursor-pointer">
                                        <option value="">Select Class</option>
                                        <?php foreach(msd_class_options() as $c): ?>
                                            <option value="<?php echo $c; ?>"><?php echo $c; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 translate-y-2 text-slate-300 pointer-events-none"></i>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 pt-4">
                            <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 pb-2 mb-4">ACADEMIC HISTORY & DOCS</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="relative group">
                                    <label class="absolute left-6 top-3 text-[10px] font-black text-blue-600 uppercase tracking-widest transition-all">Existing School</label>
                                    <input type="text" name="existing_school" class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-3xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all" placeholder="Previous School Name">
                                </div>
                                <div class="relative group">
                                    <label class="absolute left-6 top-3 text-[10px] font-black text-blue-600 uppercase tracking-widest transition-all">Passing Year</label>
                                    <select name="passing_year" class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-3xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all appearance-none cursor-pointer">
                                        <option value="">Select Year</option>
                                        <?php for($y=2024; $y<=2027; $y++) echo "<option value='$y'>$y</option>"; ?>
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 translate-y-2 text-slate-300 pointer-events-none"></i>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="relative group">
                                    <label class="absolute left-6 top-3 text-[10px] font-black text-blue-600 uppercase tracking-widest transition-all">Student ID / Roll</label>
                                    <input type="text" name="student_id" class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-3xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all" placeholder="ID Number">
                                </div>
                                <div class="relative group">
                                    <label class="absolute left-6 top-3 text-[10px] font-black text-blue-600 uppercase tracking-widest transition-all">Government ID</label>
                                    <select name="govt_id_type" class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-600 rounded-3xl px-6 pt-8 pb-3 text-sm font-bold text-slate-900 outline-none transition-all appearance-none cursor-pointer">
                                        <option value="">Select ID Type</option>
                                        <option value="Aadhaar">Aadhaar Card</option>
                                        <option value="PAN">PAN Card</option>
                                        <option value="Birth Certificate">Birth Certificate</option>
                                        <option value="APAAR ID">APAAR ID</option>
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-6 top-1/2 translate-y-2 text-slate-300 pointer-events-none"></i>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-[#FB923C] hover:bg-orange-500 text-white font-black py-5 rounded-[24px] shadow-2xl shadow-orange-500/30 transition-all hover:scale-[1.02] active:scale-95 text-sm uppercase tracking-[0.2em] mt-8">
                            Apply To All Selected Schools
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Multi-School Selection Bar -->
    <div id="selectionBar" class="fixed bottom-0 left-0 right-0 z-[4000] translate-y-full transition-transform duration-500">
        <div class="container mx-auto px-4 pb-8">
            <div class="bg-slate-900/90 backdrop-blur-2xl px-8 py-6 rounded-[32px] border border-white/10 shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)] flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg border border-blue-400/30">
                        <i class="fa-solid fa-school-flag"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-black text-lg"><span id="selectionCount">0</span> Schools Selected</h4>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Bulk application Mode active</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <button onclick="clearSelection()" class="flex-1 md:flex-none px-6 py-4 rounded-xl text-slate-400 font-black text-[10px] uppercase tracking-widest hover:text-white transition-all">Clear All</button>
                    <button onclick="openBulkEnquiry()" class="flex-1 md:flex-none px-10 py-4 bg-white text-slate-900 rounded-xl font-black text-[10px] uppercase tracking-widest hover:scale-105 active:scale-95 transition-all shadow-xl">Enquire Now</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Concierge FAB -->
    <div class="fixed bottom-8 right-8 z-[100] group hidden md:block">
        <div class="absolute -top-12 right-0 bg-slate-900 text-white text-[10px] font-black px-4 py-2 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap shadow-2xl">
            ADMISSION ASSISTANCE <i class="fa-solid fa-caret-down absolute -bottom-2 right-6 text-slate-900"></i>
        </div>
        <button onclick="openExpertModal()" class="w-16 h-16 bg-[#1D4ED8] text-white rounded-2xl shadow-[0_15px_35px_-10px_rgba(29,78,216,0.6)] flex items-center justify-center hover:scale-110 hover:rotate-12 transition-all duration-300 relative">
            <i class="fa-solid fa-comment-dots text-2xl"></i>
            <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 border-2 border-white rounded-full"></span>
        </button>
    </div>

    <script>
        let selectedSchools = JSON.parse(localStorage.getItem('msd_selection') || '[]');

        function updateSelectionUI() {
            const bar = document.getElementById('selectionBar');
            const count = document.getElementById('selectionCount');
            
            if (selectedSchools.length > 0) {
                bar.classList.remove('translate-y-full');
                count.innerText = selectedSchools.length;
            } else {
                bar.classList.add('translate-y-full');
            }

            // Update all toggle buttons on the page
            document.querySelectorAll('.select-school-btn').forEach(btn => {
                const id = btn.dataset.id;
                if (selectedSchools.some(s => s.id == id)) {
                    btn.classList.add('bg-blue-600', 'text-white');
                    btn.classList.remove('bg-slate-50', 'text-slate-900');
                    btn.innerHTML = '<i class="fa-solid fa-check mr-2"></i> Selected';
                } else {
                    btn.classList.remove('bg-blue-600', 'text-white');
                    btn.classList.add('bg-slate-50', 'text-slate-900');
                    btn.innerHTML = '<i class="fa-solid fa-plus mr-2"></i> Select';
                }
            });
        }

        function toggleSchoolSelection(id, name) {
            const index = selectedSchools.findIndex(s => s.id == id);
            if (index > -1) {
                selectedSchools.splice(index, 1);
            } else {
                selectedSchools.push({id, name});
            }
            localStorage.setItem('msd_selection', JSON.stringify(selectedSchools));
            updateSelectionUI();
        }

        function clearSelection() {
            selectedSchools = [];
            localStorage.setItem('msd_selection', '[]');
            updateSelectionUI();
        }

        function openBulkEnquiry() {
            if(selectedSchools.length === 0) return;
            
            const badgeContainer = document.getElementById('enquirySchoolBadges');
            const idContainer = document.getElementById('enquirySchoolIdContainer');
            
            badgeContainer.innerHTML = '';
            idContainer.innerHTML = '';
            
            selectedSchools.forEach(s => {
                // Add Badge
                const badge = document.createElement('div');
                badge.className = 'bg-blue-50 text-blue-600 px-4 py-2 rounded-xl text-xs font-black border border-blue-100 flex items-center gap-2';
                badge.innerHTML = `${s.name} <i class="fa-solid fa-xmark cursor-pointer opacity-50 hover:opacity-100" onclick="toggleSchoolSelection(${s.id}, '${s.name.replace("'", "\\'")}') & openBulkEnquiry()"></i>`;
                badgeContainer.appendChild(badge);
                
                // Add Hidden Input
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'school_ids[]';
                input.value = s.id;
                idContainer.appendChild(input);
            });

            const modal = document.getElementById('parentEnquiryModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function openEnquiryModal(schoolId, schoolName) {
            // Override selection for single apply
            const badgeContainer = document.getElementById('enquirySchoolBadges');
            const idContainer = document.getElementById('enquirySchoolIdContainer');
            
            badgeContainer.innerHTML = `<p class="text-2xl font-black text-blue-600 truncate">${schoolName}</p>`;
            idContainer.innerHTML = `<input type="hidden" name="school_ids[]" value="${schoolId}">`;

            const modal = document.getElementById('parentEnquiryModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEnquiryModal() {
            const modal = document.getElementById('parentEnquiryModal');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.addEventListener('DOMContentLoaded', updateSelectionUI);
    </script>
    
    <script src="<?php echo $base_url; ?>/assets/js/script.js"></script>
</body>
</html>
