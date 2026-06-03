<main class="min-h-screen bg-slate-50 flex items-center justify-center pt-24 pb-16 px-4">
    <div class="w-full max-w-md">
        
        <!-- Card Login -->
        <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
            
            <!-- Header -->
            <div class="bg-darkblue px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center">
                        <svg class="w-5 h-5 text-brandyellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-sm uppercase tracking-wider">Masuk ke Sistem</h2>
                        <p class="text-blue-200 text-[10px]">Silakan isi kredensial Anda</p>
                    </div>
                </div>
            </div>
            
            <!-- Form -->
            <div class="p-6">
                
                <!-- Alert Error -->
                <?php if($this->session->flashdata('error')): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 text-sm flex items-center gap-2" id="alert-error">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?= $this->session->flashdata('error') ?>
                </div>
                <?php endif; ?>
                
                <!-- Alert Success -->
                <?php if($this->session->flashdata('success')): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-5 text-sm flex items-center gap-2" id="alert-success">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?= $this->session->flashdata('success') ?>
                </div>
                <?php endif; ?>

                <?= form_open('auth/do_login', ['class' => 'space-y-4', 'autocomplete' => 'off']) ?>
                    
                    <!-- Username -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input type="text" name="username" 
                                   class="w-full pl-10 pr-4 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white"
                                   placeholder="Masukkan username"
                                   value="<?= set_value('username') ?>"
                                   required autofocus>
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input type="password" name="password" id="password"
                                   class="w-full pl-10 pr-12 py-3 border-2 border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brandyellow focus:border-brandyellow bg-white"
                                   placeholder="Masukkan password"
                                   required>
                            <button type="button" onclick="togglePassword()" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                                <svg id="eye-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg id="eye-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" value="1"
                                   class="w-4 h-4 rounded border-slate-300 text-brandyellow focus:ring-brandyellow">
                            <span class="text-slate-600">Ingat saya</span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" 
                            class="w-full bg-brandyellow hover:bg-yellow-400 text-darkblue font-bold py-3 rounded-xl text-sm transition-all shadow-lg shadow-brandyellow/20 active:scale-[0.98] flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        Masuk
                    </button>
                    
                <?= form_close() ?>
            </div>
            
            <!-- Footer -->
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-100">
                <p class="text-center text-xs text-slate-500">
                    &copy; <?= date('Y') ?> <span class="font-semibold text-darkblue">BBWS Mesuji Sekampung</span>
                </p>
            </div>
        </div>
    </div>
</main>

<script>
    function togglePassword() {
        var input = document.getElementById('password');
        var open = document.getElementById('eye-open');
        var closed = document.getElementById('eye-closed');
        
        if (input.type === 'password') {
            input.type = 'text';
            open.classList.add('hidden');
            closed.classList.remove('hidden');
        } else {
            input.type = 'password';
            open.classList.remove('hidden');
            closed.classList.add('hidden');
        }
    }

    // Auto-hide alerts
    setTimeout(function() {
        var error = document.getElementById('alert-error');
        var success = document.getElementById('alert-success');
        if (error) error.style.display = 'none';
        if (success) success.style.display = 'none';
    }, 5000);
</script>