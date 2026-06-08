<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AssetCare - Enterprise Management</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

    <style>
        html { scroll-behavior: smooth; }
        .glass-nav-premium { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.6); }
        .hover-lift { transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.4s ease; }
        .hover-lift:hover { transform: translateY(-5px) scale(1.005); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
        .smooth-btn { transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        .smooth-btn:hover { transform: translateY(-2px); filter: brightness(1.05); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .smooth-btn:active { transform: translateY(1px) scale(0.97); filter: brightness(0.95); box-shadow: 0 5px 10px rgba(0,0,0,0.05); }
        .input-smooth { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .input-smooth:focus { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(59, 130, 246, 0.1); }
        .animate-pop { animation: popIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards; opacity: 0; }
        @keyframes popIn { 0% { opacity: 0; transform: translateY(20px) scale(0.98); } 100% { opacity: 1; transform: translateY(0) scale(1); } }
    </style>
</head>
<body class="bg-[#F5F5F7] font-sans text-gray-900 overflow-x-hidden">

<div id="app" class="min-h-screen flex flex-col relative">
    
    <div v-if="isInitialLoading" class="fixed inset-0 bg-[#F5F5F7] z-[9999] flex flex-col items-center justify-center transition-opacity duration-500">
        <i class="fas fa-circle-notch fa-spin text-5xl text-blue-500 mb-6 drop-shadow-md"></i>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight mb-2">AssetCare</h1>
        <p class="text-sm font-medium text-gray-500 uppercase tracking-widest animate-pulse">Establishing Secure Connection...</p>
    </div>

    <div v-if="showBulkDuplicateModal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-[100] flex items-center justify-center p-4 animate-pop">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-8 border border-gray-100 relative">
            <button @click="closeDuplicateModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition-colors"><i class="fas fa-times text-xl"></i></button>
            <div class="flex items-center gap-3 mb-6 text-orange-500">
                <i class="fas fa-exclamation-triangle text-3xl"></i>
                <h2 class="text-xl font-bold text-gray-900 tracking-tight">Duplicate Assets Blocked</h2>
            </div>
            <p class="text-sm text-gray-600 mb-4">The smart detection system skipped the following <b>{{ bulkDuplicates.length }}</b> assets because their Tag or Serial Number already exists in the inventory. All other valid assets were successfully imported.</p>
            <div class="max-h-60 overflow-y-auto bg-gray-50 rounded-xl border border-gray-200 p-2 mb-6 shadow-inner">
                <div v-for="dup in bulkDuplicates" :key="dup.tag" class="p-3 border-b border-gray-100 last:border-0 flex justify-between items-center text-sm">
                    <span class="font-bold text-gray-800"><i class="fas fa-tag text-gray-400 mr-2 text-xs"></i>{{ dup.tag }}</span>
                    <span class="font-mono text-xs text-gray-500">SN: {{ dup.serial }}</span>
                </div>
            </div>
            <button @click="closeDuplicateModal" class="w-full bg-blue-500 text-white font-medium py-3 rounded-xl smooth-btn">Understood, View Inventory</button>
        </div>
    </div>

    <div v-if="isWaitingApproval" class="min-h-screen bg-[#F5F5F7] flex flex-col items-center justify-center p-6 text-center animate-pop">
        <div class="max-w-md bg-white p-10 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 hover-lift">
            <i class="fas fa-clock text-6xl text-orange-400 mb-6 animate-pulse"></i>
            <h1 class="text-2xl font-semibold text-gray-900 mb-4 tracking-tight">Waiting for Admin Approval</h1>
            <p class="text-gray-500 mb-8 text-sm">Your account has been created successfully, but a system administrator needs to verify your identity before you can gain access. Please wait for a moment.</p>
            <button @click="isWaitingApproval = false; authView = 'login'" class="w-full bg-gray-900 text-white px-8 py-3 rounded-xl font-medium smooth-btn">Back to Login</button>
        </div>
    </div>

    <div v-else-if="!currentUser" class="min-h-screen flex items-center justify-center bg-[#F5F5F7] p-4">
        <div class="bg-white p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] w-full max-w-md border border-gray-100 animate-pop hover-lift">
            <div class="text-center mb-8">
                <i class="fas fa-shield-alt text-4xl text-blue-500 mb-3 hover:rotate-12 transition-transform duration-500"></i>
                <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">AssetCare</h1>
            </div>

            <div v-if="authView === 'login'" class="space-y-4 animate-pop" style="animation-delay: 0.1s;">
                <input v-model="authForm.username" type="text" placeholder="Username" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm">
                <input v-model="authForm.password" type="password" placeholder="Password" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm">
                <button @click="login" class="w-full bg-blue-500 text-white font-medium py-3 rounded-xl smooth-btn mt-2">Sign In</button>
                <div class="flex justify-between mt-4">
                    <button @click="authView = 'forgot'" class="text-sm text-blue-500 hover:text-blue-600 transition-colors">Forgot Password?</button>
                    <button @click="authView = 'register'" class="text-sm text-gray-500 hover:text-gray-800 transition-colors">Create Account</button>
                </div>
            </div>

            <div v-if="authView === 'register'" class="space-y-4 animate-pop">
                <input v-model="authForm.email" type="email" placeholder="Email (@gmail.com or @quantanite.com)" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm">
                <input v-model="authForm.username" type="text" placeholder="Choose Username" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1.5 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm">
                <input v-model="authForm.password" type="password" placeholder="Password" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1.5 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm">
                <input v-model="authForm.confirmPassword" type="password" placeholder="Confirm Password" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1.5 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm">
                <button @click="register" class="w-full bg-blue-500 text-white font-medium py-3 rounded-xl smooth-btn mt-2">Create Account</button>
                <button @click="authView = 'login'" class="w-full text-sm text-gray-500 mt-2 hover:text-gray-800 transition-colors">Back to Login</button>
            </div>

            <div v-if="authView === 'forgot'" class="space-y-4 animate-pop">
                <p class="text-sm text-gray-500 mb-2">Enter your credentials to verify your identity.</p>
                <input v-model="forgotForm.username" type="text" placeholder="Your Username" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm">
                <input v-model="forgotForm.email" type="email" placeholder="Your Registered Email" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm">
                <button @click="verifyForgotIdentity" class="w-full bg-gray-900 text-white font-medium py-3 rounded-xl smooth-btn mt-2">Verify Identity</button>
                <button @click="authView = 'login'" class="w-full text-sm text-gray-500 mt-2 hover:text-gray-800 transition-colors">Back to Login</button>
            </div>

            <div v-if="authView === 'reset'" class="space-y-4 animate-pop">
                <p class="text-sm text-green-600 font-medium mb-2"><i class="fas fa-check-circle mr-1"></i> Identity verified. Enter your new password.</p>
                <input v-model="forgotForm.newPassword" type="password" placeholder="New Password" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm">
                <input v-model="forgotForm.confirmPassword" type="password" placeholder="Confirm New Password" class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm">
                <button @click="resetPassword" class="w-full bg-blue-500 text-white font-medium py-3 rounded-xl smooth-btn mt-2">Save New Password</button>
                <button @click="authView = 'login'" class="w-full text-sm text-gray-500 mt-2 hover:text-gray-800 transition-colors">Cancel</button>
            </div>

            <div v-if="authMessage" class="mt-6 p-3 text-sm text-center rounded-xl font-medium animate-pop" :class="messageColor">{{ authMessage }}</div>
        </div>
    </div>

    <div v-else class="flex-grow flex flex-col animate-pop">
        
        <div class="sticky top-0 z-50 p-4 w-full bg-[#F5F5F7]/70 backdrop-blur-md">
            <nav class="max-w-7xl mx-auto glass-nav-premium rounded-3xl shadow-[0_8px_32px_rgba(0,0,0,0.06)] text-gray-900 p-2 flex flex-wrap justify-between items-center transition-all duration-500 hover:shadow-[0_12px_48px_rgba(0,0,0,0.09)]">
                
                <div class="flex items-center gap-3 pl-2 cursor-pointer group">
                    <div class="w-11 h-11 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-105 group-hover:rotate-3 transition-all duration-400">
                        <i class="fas fa-laptop-medical text-white text-xl"></i>
                    </div>
                    <div class="flex flex-col justify-center hidden sm:flex">
                        <h1 class="text-[17px] font-bold tracking-tight text-gray-900 leading-tight">AssetCare</h1>
                        <span class="text-[9px] font-black uppercase tracking-widest text-blue-600 leading-none">{{ currentUser.role }}</span>
                    </div>
                </div>

                <div class="flex items-center p-1.5 bg-gray-100/60 rounded-2xl border border-gray-200/50 overflow-x-auto no-scrollbar">
                    <button @click="activeTab = 'dashboard'" :class="tabClass('dashboard')" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm transition-all duration-300 relative group outline-none">
                        <i class="fas fa-chart-pie" :class="activeTab === 'dashboard' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600'"></i><span class="hidden xl:inline">Dashboard</span>
                    </button>
                    <button @click="activeTab = 'inventory'" :class="tabClass('inventory')" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm transition-all duration-300 relative group outline-none">
                        <i class="fas fa-boxes" :class="activeTab === 'inventory' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600'"></i><span class="hidden xl:inline">Inventory</span>
                    </button>
                    
                    <button @click="activeTab = 'slotDetails'" :class="tabClass('slotDetails')" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm transition-all duration-300 relative group outline-none">
                        <i class="fas fa-list-ol" :class="activeTab === 'slotDetails' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600'"></i><span class="hidden xl:inline">Slot Details</span>
                    </button>
                    
                    <button v-if="currentUser.role === 'admin'" @click="activeTab = 'monthlyReport'" :class="tabClass('monthlyReport')" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm transition-all duration-300 relative group outline-none">
                        <i class="fas fa-calendar-alt" :class="activeTab === 'monthlyReport' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600'"></i><span class="hidden xl:inline">Monthly Report</span>
                    </button>

                    <button @click="activeTab = 'add'" :class="tabClass('add')" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm transition-all duration-300 relative group outline-none">
                        <i class="fas fa-plus-circle" :class="activeTab === 'add' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600'"></i><span class="hidden xl:inline">Add Device</span>
                    </button>
                    <button @click="activeTab = 'profile'" :class="tabClass('profile')" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm transition-all duration-300 relative group outline-none">
                        <i class="fas fa-user-circle" :class="activeTab === 'profile' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600'"></i><span class="hidden xl:inline">Profile</span>
                    </button>
                    
                    <button v-if="['admin', 'moderator'].includes(currentUser.role)" @click="activeTab = 'adminPanel'" :class="tabClass('adminPanel')" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm transition-all duration-300 relative group outline-none">
                        <i class="fas fa-shield-alt" :class="activeTab === 'adminPanel' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600'"></i><span class="hidden xl:inline">Admin</span>
                        <span v-if="(pendingUsers.length > 0 || pendingAssets.length > 0) && currentUser.role === 'admin'" class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[8px] font-bold text-white animate-pulse shadow-sm border border-white">{{ pendingUsers.length + pendingAssets.length }}</span>
                    </button>
                    
                    <button v-if="['admin', 'moderator'].includes(currentUser.role)" @click="activeTab = 'bulk'" :class="tabClass('bulk')" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm transition-all duration-300 relative group outline-none">
                        <i class="fas fa-layer-group" :class="activeTab === 'bulk' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600'"></i><span class="hidden xl:inline">Bulk</span>
                    </button>
                </div>

                <div class="pr-2 flex items-center gap-3">
                    <button @click="logout" class="w-11 h-11 flex items-center justify-center rounded-2xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-300 hover:shadow-lg hover:shadow-red-500/30 group outline-none">
                        <i class="fas fa-power-off text-lg group-hover:scale-110 transition-transform"></i>
                    </button>
                </div>
            </nav>
        </div>

        <main class="flex-grow p-4 md:p-6 max-w-7xl mx-auto w-full relative">
            
            <div v-if="activeTab === 'dashboard'" class="space-y-8 animate-pop">
                <div class="flex items-center justify-between">
                    <div><h2 class="text-2xl font-semibold tracking-tight text-gray-900">Operational Overview</h2><p class="text-gray-500 text-sm mt-1">Real-time statistics and asset health monitoring.</p></div>
                    <div class="text-right"><p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">System Time</p><p class="text-sm font-medium text-gray-800">{{ new Date().toLocaleDateString() }}</p></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 text-gray-800 hover-lift group">
                        <div class="flex justify-between items-start mb-4"><i class="fas fa-boxes text-2xl text-blue-500 group-hover:scale-110 transition-transform duration-300"></i><span class="text-[10px] bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider">Inventory</span></div>
                        <p class="text-4xl font-semibold mb-1 tracking-tight">{{ inventoryAssets.length }}</p><p class="text-xs font-medium text-gray-500">Total Registered Assets</p>
                    </div>
                    <div class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 text-gray-800 hover-lift group">
                        <div class="flex justify-between items-start mb-4"><i class="fas fa-tools text-2xl text-orange-500 group-hover:scale-110 transition-transform duration-300"></i><span class="text-[10px] bg-orange-50 text-orange-600 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider">Live</span></div>
                        <p class="text-4xl font-semibold mb-1 tracking-tight">{{ inRepairCount }}</p><p class="text-xs font-medium text-gray-500">Currently in Repair</p>
                    </div>
                    <div class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 text-gray-800 hover-lift group">
                        <div class="flex justify-between items-start mb-4"><i class="fas fa-history text-2xl text-emerald-500 group-hover:rotate-12 transition-transform duration-300"></i><span class="text-[10px] bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider">Success</span></div>
                        <p class="text-4xl font-semibold mb-1 tracking-tight">{{ allTimeRepairedCount }}</p><p class="text-xs font-medium text-gray-500">All-Time Repair Actions</p>
                    </div>
                    
                    <div class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 text-gray-800 hover-lift group">
                        <div class="flex justify-between items-start mb-4"><i class="fas fa-truck text-2xl text-teal-500 group-hover:translate-x-2 transition-transform duration-300"></i><span class="text-[10px] bg-teal-50 text-teal-600 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider">Delivered</span></div>
                        <p class="text-4xl font-semibold mb-1 tracking-tight">{{ deliveredCount }}</p><p class="text-xs font-medium text-gray-500">Delivered to IT</p>
                    </div>

                    <div v-if="['admin', 'moderator'].includes(currentUser.role)" class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 text-gray-800 hover-lift group">
                        <div class="flex justify-between items-start mb-4"><i class="fas fa-user-check text-2xl text-purple-500 group-hover:scale-110 transition-transform duration-300"></i><span class="text-[10px] bg-purple-50 text-purple-600 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider">Users</span></div>
                        <p class="text-4xl font-semibold mb-1 tracking-tight">{{ totalActiveUserCount }}</p><p class="text-xs font-medium text-gray-500">Total Active Users</p>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 hover-lift">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Status Breakdown</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
                        <div v-for="st in ['In Assessment', 'Quick Repair Stage', 'Complex Stage', 'Ready', 'Irreparable', 'EOL/ Disposed']" :key="st" class="relative">
                            <p class="text-[10px] font-bold uppercase text-gray-500 mb-1 tracking-wider">{{ st }}</p>
                            <p class="text-2xl font-semibold text-gray-800">{{ statusCounts[st] || 0 }}</p>
                            <div class="mt-3 h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-1000 ease-out" :class="statusBg(st)" :style="{ width: ( (statusCounts[st] || 0) / (inventoryAssets.length || 1) * 100) + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'inventory'" class="space-y-6 animate-pop">
                <div class="glass-nav-premium p-4 rounded-2xl shadow-sm border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-4 sticky top-[100px] z-40">
                    <div class="flex items-center gap-3 bg-white/60 p-3 rounded-xl border border-gray-200 focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-100 transition-all">
                        <i class="fas fa-search text-gray-400"></i><input v-model="searchQuery" type="text" placeholder="Search Tag or Serial..." class="w-full outline-none bg-transparent text-sm">
                    </div>
                    <div class="flex items-center gap-3 bg-white/60 p-3 rounded-xl border border-gray-200 focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-100 transition-all">
                        <i class="fas fa-filter text-gray-400"></i>
                        <select v-model="statusFilter" class="w-full bg-transparent outline-none text-sm text-gray-700">
                            <option value="">All Statuses</option><option v-for="st in statusOptions" :value="st">{{ st }}</option>
                        </select>
                    </div>
                </div>

                <div v-for="asset in paginatedAssets" :key="asset.tag" class="bg-white rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 overflow-hidden mb-4 hover-lift">
                    <div class="p-5 bg-gray-50/50 border-b border-gray-100 flex flex-wrap justify-between items-center gap-4">
                        <div class="flex-grow">
                            <div class="flex items-center gap-3">
                                <span class="bg-gray-200/70 text-gray-700 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">{{ asset.type || 'Asset' }}</span>
                                <h2 class="text-lg font-semibold text-gray-900 tracking-tight">{{ asset.tag }} &middot; <span class="font-normal text-gray-600">{{ asset.brand }} {{ asset.model }}</span></h2>
                            </div>
                            <div class="flex gap-4 mt-2 items-center">
                                <p class="text-xs font-mono text-gray-500 bg-white px-2 py-0.5 rounded border border-gray-100">SN: {{ asset.serial }}</p>
                                <p class="text-[10px] text-orange-600 font-bold uppercase tracking-wider">Age: {{ calculateAge(asset.purchaseDate) }}</p>
                                <p class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider bg-emerald-50 px-2 py-0.5 rounded-md">Total Cost: ৳{{ calculateTotalRepairCost(asset) }}</p>
                                <p v-if="asset.repairCount" class="text-[10px] text-blue-600 font-bold uppercase tracking-wider bg-blue-50 px-2 py-0.5 rounded-md">Repaired: {{ asset.repairCount }} times</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <select v-if="['admin', 'moderator'].includes(currentUser.role)" v-model="asset.status" @change="updateStatus(asset)" class="p-2.5 rounded-xl font-medium text-xs border outline-none cursor-pointer transition-colors" :class="statusColor(asset.status)">
                                <option v-for="st in statusOptions" :value="st">{{ st }}</option>
                            </select>
                            <div v-else class="px-3 py-2.5 rounded-xl font-bold text-xs border cursor-default" :class="statusColor(asset.status)">
                                {{ asset.status }}
                            </div>
                            
                            <button v-if="currentUser.role === 'admin'" @click="deleteAsset(asset.tag)" class="text-red-400 hover:text-red-600 p-2 transition-transform duration-300 hover:scale-110"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    
                    <div class="p-5 grid md:grid-cols-2 gap-8">
                        <div class="text-sm">
                            <p class="font-bold mb-3 text-gray-400 uppercase tracking-widest text-[10px]">Technical History</p>
                            <div class="max-h-48 overflow-y-auto space-y-3 pr-2 smooth-scroll">
                                <template v-for="rep in asset.repairs">
                                    <div class="bg-gray-50/50 p-3 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden transition-all hover:bg-gray-100">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-400"></div>
                                        <div class="flex justify-between font-medium text-gray-800 ml-2">
                                            <span>{{ rep.problem }} <span v-if="rep.repSerial" class="block text-xs text-gray-500 font-normal mt-0.5"><i class="fas fa-barcode mr-1"></i>S/N: {{ rep.repSerial }}</span></span>
                                            <span class="text-green-600 font-semibold">৳{{ rep.cost }}</span>
                                        </div>
                                        <p class="text-right text-[10px] text-gray-400 mt-1 font-medium">{{ rep.date }}</p>
                                    </div>
                                </template>
                                <p v-if="!asset.repairs || !asset.repairs.length" class="text-gray-400 text-sm py-4 text-center">No history documented yet.</p>
                            </div>
                        </div>
                        
                        <div v-if="['admin', 'moderator'].includes(currentUser.role)" class="bg-white p-5 rounded-3xl border border-gray-100 shadow-[0_2px_10px_rgb(0,0,0,0.02)] space-y-3">
                            <p class="font-bold mb-1 text-gray-400 uppercase tracking-widest text-[10px]">Log Repair / Replacement</p>
                            <input v-model="getDraft(asset.tag).problem" placeholder="Replaced Item Description" class="w-full p-3 bg-gray-50 border border-gray-200 text-sm rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth">
                            <input v-model="getDraft(asset.tag).repSerial" placeholder="Replacement Part Serial / S/N" class="w-full p-3 bg-gray-50 border border-gray-200 text-sm rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth">
                            
                            <div class="flex gap-3">
                                <input v-model="getDraft(asset.tag).cost" type="number" placeholder="Repair Cost (৳)" class="w-full p-3 bg-gray-50 border border-gray-200 text-sm rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth">
                                <input v-model="getDraft(asset.tag).date" type="date" class="w-full p-3 bg-gray-50 border border-gray-200 text-sm rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-gray-600">
                            </div>
                            
                            <button @click="addRepair(asset.tag)" class="w-full bg-blue-500 text-white font-medium py-3 rounded-xl text-sm smooth-btn mt-2">Update Asset History</button>
                        </div>
                    </div>
                </div>

                <div v-if="filteredAssets.length > 0" class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-4 p-4 bg-white rounded-2xl border border-gray-200 shadow-sm animate-pop">
                    <div class="text-sm text-gray-500 font-medium">
                        Showing <span class="font-bold text-gray-800">{{ (currentPage - 1) * itemsPerPage + 1 }}</span> to 
                        <span class="font-bold text-gray-800">{{ Math.min(currentPage * itemsPerPage, filteredAssets.length) }}</span> of 
                        <span class="font-bold text-gray-800">{{ filteredAssets.length }}</span> assets
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button @click="currentPage = 1" :disabled="currentPage === 1" class="w-9 h-9 flex items-center justify-center bg-gray-50 border border-gray-200 rounded-xl text-gray-600 disabled:opacity-40 disabled:pointer-events-none smooth-btn text-xs hover:bg-gray-100 hover:text-blue-600"><i class="fas fa-angle-double-left"></i></button>
                        <button @click="currentPage--" :disabled="currentPage === 1" class="px-3 h-9 flex items-center justify-center bg-gray-50 border border-gray-200 rounded-xl text-gray-600 text-xs font-semibold disabled:opacity-40 disabled:pointer-events-none smooth-btn hover:bg-gray-100 hover:text-blue-600">Prev</button>
                        
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-4 py-2 rounded-xl border border-blue-100 mx-1">
                            Page {{ currentPage }} of {{ totalPages }}
                        </span>

                        <button @click="currentPage++" :disabled="currentPage >= totalPages" class="px-3 h-9 flex items-center justify-center bg-gray-50 border border-gray-200 rounded-xl text-gray-600 text-xs font-semibold disabled:opacity-40 disabled:pointer-events-none smooth-btn hover:bg-gray-100 hover:text-blue-600">Next</button>
                        <button @click="currentPage = totalPages" :disabled="currentPage >= totalPages" class="w-9 h-9 flex items-center justify-center bg-gray-50 border border-gray-200 rounded-xl text-gray-600 disabled:opacity-40 disabled:pointer-events-none smooth-btn text-xs hover:bg-gray-100 hover:text-blue-600"><i class="fas fa-angle-double-right"></i></button>
                    </div>
                </div>

            </div>

            <div v-if="activeTab === 'slotDetails'" class="space-y-6 animate-pop">
                <div class="flex items-center justify-between">
                    <div><h2 class="text-2xl font-semibold tracking-tight text-gray-900">Repair Slot Details</h2><p class="text-gray-500 text-sm mt-1">Manage and track bulk repair slot assignments.</p></div>
                </div>

                <div v-if="['admin', 'moderator'].includes(currentUser.role)" class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 hover-lift">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-widest mb-4 flex items-center gap-2"><i class="fas fa-plus-square text-blue-500"></i> Create New Slot Record</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">S/N</label><input v-model="slotForm.sn" type="text" placeholder="Serial No" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                        <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Date</label><input v-model="slotForm.date_val" type="date" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm text-gray-600"></div>
                        <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Slot No</label><input v-model="slotForm.slotNo" type="text" placeholder="Ex: S-01" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                        <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Slot Name</label><input v-model="slotForm.slotName" type="text" placeholder="Ex: Dell Core i5 Slot" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                        
                        <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Total Assets</label><input v-model="slotForm.totalAssets" type="number" placeholder="Count" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                        <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Return to IT</label><input v-model="slotForm.returnToIT" type="number" placeholder="Count" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                        <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">EOL / Disposed</label><input v-model="slotForm.eol" type="number" placeholder="Count" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                        <div><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Pending</label><input v-model="slotForm.pending" type="number" placeholder="Count" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                        <div class="col-span-2 md:col-span-4"><label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1">Remarks</label><input v-model="slotForm.remarks" type="text" placeholder="Notes" class="w-full bg-gray-50 border border-gray-200 p-2.5 rounded-xl mt-1 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-sm"></div>
                    </div>
                    <div class="flex justify-end gap-3 mt-4">
                        <button @click="clearSlotForm" class="bg-gray-100 text-gray-600 font-semibold px-6 py-2.5 rounded-xl smooth-btn text-sm hover:bg-gray-200">Clear</button>
                        <button @click="submitSlot" class="bg-blue-500 text-white font-semibold px-6 py-2.5 rounded-xl smooth-btn text-sm shadow-md">Submit Slot Record</button>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left min-w-max">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] font-black uppercase text-slate-500 border-b border-gray-100">
                                    <th class="p-4">S/N</th>
                                    <th class="p-4">Date</th>
                                    <th class="p-4">Slot No</th>
                                    <th class="p-4">Slot Name</th> 
                                    <th class="p-4 text-center">Total Assets</th>
                                    <th class="p-4 text-center">Return to IT</th>
                                    <th class="p-4 text-center">EOL</th>
                                    <th class="p-4 text-center">Pending</th>
                                    <th class="p-4">Remarks</th>
                                    <th v-if="['admin', 'moderator'].includes(currentUser.role)" class="p-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="slot in slots" :key="slot.id" class="border-b border-gray-50 hover:bg-slate-50/50 transition-colors">
                                    <template v-if="editingSlotId === slot.id">
                                        <td class="p-2"><input v-model="slot.sn" class="w-full bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-sm font-semibold text-gray-800 p-1"></td>
                                        <td class="p-2"><input v-model="slot.date_val" type="date" class="w-full bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-xs text-gray-500 p-1"></td>
                                        <td class="p-2"><input v-model="slot.slotNo" class="w-full bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-sm font-bold text-blue-600 p-1"></td>
                                        <td class="p-2"><input v-model="slot.slotName" class="w-full bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-sm font-medium text-gray-700 p-1"></td>
                                        <td class="p-2"><input v-model="slot.totalAssets" type="number" class="w-full text-center bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-sm p-1"></td>
                                        <td class="p-2"><input v-model="slot.returnToIT" type="number" class="w-full text-center bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-sm text-teal-600 font-semibold p-1"></td>
                                        <td class="p-2"><input v-model="slot.eol" type="number" class="w-full text-center bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-sm text-gray-500 font-semibold p-1"></td>
                                        <td class="p-2"><input v-model="slot.pending" type="number" class="w-full text-center bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-sm text-orange-500 font-semibold p-1"></td>
                                        <td class="p-2"><input v-model="slot.remarks" class="w-full bg-transparent border-b border-gray-300 focus:border-blue-400 outline-none text-xs text-gray-500 p-1"></td>
                                        <td class="p-2 text-center flex justify-center gap-2 mt-1">
                                            <button @click="saveSlotInline(slot)" class="text-green-500 hover:text-green-700 p-1 transition-transform hover:scale-110" title="Save"><i class="fas fa-save"></i></button>
                                            <button @click="cancelEdit()" class="text-gray-400 hover:text-gray-600 p-1 transition-transform hover:scale-110" title="Cancel"><i class="fas fa-times"></i></button>
                                        </td>
                                    </template>
                                    
                                    <template v-else>
                                        <td class="p-4 text-sm font-semibold text-gray-800">{{ slot.sn }}</td>
                                        <td class="p-4 text-xs text-gray-500">{{ slot.date_val }}</td>
                                        <td class="p-4 text-sm font-bold text-blue-600">{{ slot.slotNo }}</td>
                                        <td class="p-4 text-sm font-medium text-gray-700">{{ slot.slotName || 'N/A' }}</td>
                                        <td class="p-4 text-center text-sm">{{ slot.totalAssets }}</td>
                                        <td class="p-4 text-center text-sm text-teal-600 font-semibold">{{ slot.returnToIT }}</td>
                                        <td class="p-4 text-center text-sm text-gray-500 font-semibold">{{ slot.eol }}</td>
                                        <td class="p-4 text-center text-sm text-orange-500 font-semibold">{{ slot.pending }}</td>
                                        <td class="p-4 text-xs text-gray-500">{{ slot.remarks }}</td>
                                        <td v-if="['admin', 'moderator'].includes(currentUser.role)" class="p-4 text-center flex justify-center gap-2">
                                            <button @click="editSlot(slot.id)" class="text-blue-500 hover:text-blue-700 p-1 transition-transform hover:scale-110" title="Edit"><i class="fas fa-edit"></i></button>
                                            <button @click="deleteSlot(slot.id)" class="text-red-400 hover:text-red-600 p-1 transition-transform hover:scale-110" title="Delete"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </template>
                                </tr>
                                <tr v-if="slots.length === 0">
                                    <td colspan="10" class="p-8 text-center text-gray-400 text-sm">No slot records found.</td>
                                </tr>
                            </tbody>
                            <tfoot v-if="slots.length > 0" class="bg-blue-50/50 border-t-2 border-blue-100 font-bold text-sm text-gray-800">
                                <tr>
                                    <td colspan="4" class="p-4 text-right uppercase tracking-wider text-[11px] text-gray-500">Overall Totals:</td>
                                    <td class="p-4 text-center">{{ slotTotals.totalAssets }}</td>
                                    <td class="p-4 text-center text-teal-700">{{ slotTotals.returnToIT }}</td>
                                    <td class="p-4 text-center text-gray-700">{{ slotTotals.eol }}</td>
                                    <td class="p-4 text-center text-orange-600">{{ slotTotals.pending }}</td>
                                    <td class="p-4"></td>
                                    <td v-if="['admin', 'moderator'].includes(currentUser.role)" class="p-4"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'monthlyReport' && currentUser.role === 'admin'" class="space-y-6 animate-pop">
                <div class="flex items-center justify-between">
                    <div><h2 class="text-2xl font-semibold tracking-tight text-gray-900">Monthly Report Overview</h2><p class="text-gray-500 text-sm mt-1">Review statistical analysis and monthly repair costs directly from inventory history.</p></div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 flex justify-center items-center gap-6 hover-lift">
                    <div class="flex flex-col w-48">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1 mb-1">Select Month</label>
                        <select v-model="reportFilterMonth" class="bg-gray-50 border border-gray-200 p-2.5 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 text-sm font-medium text-gray-700 cursor-pointer w-full">
                            <option value="All">All Months</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>
                    <div class="flex flex-col w-48">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider ml-1 mb-1">Select Year</label>
                        <select v-model="reportFilterYear" class="bg-gray-50 border border-gray-200 p-2.5 rounded-xl outline-none focus:ring-2 focus:ring-blue-500/50 text-sm font-medium text-gray-700 cursor-pointer w-full">
                            <option value="All">All Years</option>
                            <option v-for="y in [2023, 2024, 2025, 2026, 2027, 2028, 2029, 2030, 2031, 2032]" :value="y">{{ y }}</option>
                        </select>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 overflow-hidden hover-lift">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left min-w-max">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] font-black uppercase text-slate-500 border-b border-gray-100">
                                    <th class="p-4 w-16 text-center">#</th>
                                    <th class="p-4">Month & Year</th>
                                    <th class="p-4 text-center">Total Assets Repaired</th>
                                    <th class="p-4 text-right">Total Monthly Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, idx) in monthlyReportData" :key="row.key" class="border-b border-gray-50 hover:bg-slate-50/50 transition-colors">
                                    <td class="p-4 text-sm font-bold text-gray-400 text-center">{{ idx + 1 }}</td>
                                    <td class="p-4 text-sm font-bold text-blue-600">{{ row.monthName }} {{ row.year }}</td>
                                    <td class="p-4 text-center text-sm font-semibold text-gray-800">{{ row.assetCount }} Assets</td>
                                    <td class="p-4 text-right text-sm font-bold text-emerald-600">৳{{ row.cost.toLocaleString() }}</td>
                                </tr>
                                <tr v-if="monthlyReportData.length === 0">
                                    <td colspan="4" class="p-8 text-center text-gray-400 text-sm">No repair data found for the selected period.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'adminPanel' && ['admin', 'moderator'].includes(currentUser.role)" class="space-y-6 animate-pop">
                <div v-if="currentUser.role === 'admin' && pendingUsers.length > 0" class="bg-white p-8 rounded-xl shadow-lg border-2 border-orange-200 hover-lift">
                    <div class="flex items-center justify-between mb-4 border-b pb-4">
                        <h2 class="text-xl font-bold flex items-center gap-2 text-orange-600"><i class="fas fa-user-plus"></i> Pending Approval Requests</h2>
                        <span class="bg-orange-100 text-orange-700 text-xs font-black px-3 py-1 rounded-full uppercase">Action Required</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] font-black uppercase text-slate-500">
                                    <th class="p-4">Requested Date/Time</th><th class="p-4">Username</th><th class="p-4">Email Address</th><th class="p-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="u in pendingUsers" :key="u.username" class="border-b hover:bg-slate-50 transition-colors">
                                    <td class="p-4 text-xs font-mono text-slate-500">{{ u.requestDate || 'N/A' }}</td>
                                    <td class="p-4 font-bold">{{ u.username }}</td>
                                    <td class="p-4 text-sm">{{ u.email }}</td>
                                    <td class="p-4 flex justify-center gap-3">
                                        <button @click="approveUser(u.username)" class="bg-green-600 text-white text-xs font-bold px-4 py-2 rounded smooth-btn"><i class="fas fa-check mr-1"></i> Accept</button>
                                        <button @click="rejectUser(u.username)" class="bg-red-500 text-white text-xs font-bold px-4 py-2 rounded smooth-btn"><i class="fas fa-times mr-1"></i> Reject</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div v-if="currentUser.role === 'admin' && pendingAssets.length > 0" class="bg-white p-8 rounded-xl shadow-lg border-2 border-blue-200 hover-lift">
                    <div class="flex items-center justify-between mb-4 border-b pb-4">
                        <h2 class="text-xl font-bold flex items-center gap-2 text-blue-600"><i class="fas fa-box-open"></i> Pending Asset Approvals</h2>
                        <span class="bg-blue-100 text-blue-700 text-xs font-black px-3 py-1 rounded-full uppercase">Action Required</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] font-black uppercase text-slate-500">
                                    <th class="p-4">Type</th><th class="p-4">Brand & Model</th><th class="p-4">Asset Tag</th><th class="p-4">Serial</th><th class="p-4">Purchase Date</th><th class="p-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="a in pendingAssets" :key="a.tag" class="border-b hover:bg-slate-50 transition-colors">
                                    <td class="p-4 text-sm">{{ a.type }}</td>
                                    <td class="p-4 text-sm">{{ a.brand }} {{ a.model }}</td>
                                    <td class="p-4 font-bold">{{ a.tag }}</td>
                                    <td class="p-4 text-xs font-mono text-slate-500">{{ a.serial }}</td>
                                    <td class="p-4 text-xs text-slate-500">{{ a.purchaseDate || 'N/A' }}</td>
                                    <td class="p-4 flex justify-center gap-3">
                                        <button @click="approveAsset(a.tag)" class="bg-green-600 text-white text-xs font-bold px-4 py-2 rounded smooth-btn"><i class="fas fa-check mr-1"></i> Approve</button>
                                        <button @click="rejectAsset(a.tag)" class="bg-red-500 text-white text-xs font-bold px-4 py-2 rounded smooth-btn"><i class="fas fa-times mr-1"></i> Reject</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-xl shadow border hover-lift">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2 border-b pb-2"><i class="fas fa-chart-line text-purple-600"></i> Reporting & Data Export</h2>
                    <p class="text-sm text-gray-500 mb-6">Export full system inventory data for audits and external reporting.</p>
                    <div class="flex flex-wrap gap-4">
                        <button @click="exportCSV" class="bg-green-600 text-white font-bold py-3 px-6 rounded shadow flex items-center gap-2 smooth-btn"><i class="fas fa-file-excel"></i> Export to Excel (CSV)</button>
                        <button @click="exportPDF" class="bg-red-600 text-white font-bold py-3 px-6 rounded shadow flex items-center gap-2 smooth-btn"><i class="fas fa-file-pdf"></i> Export to PDF</button>
                    </div>
                </div>

                <div v-if="currentUser.role === 'admin'" class="bg-white p-8 rounded-xl shadow border hover-lift">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h2 class="text-xl font-bold flex items-center gap-2"><i class="fas fa-users-cog text-blue-600"></i> User & Role Management</h2>
                        <span class="text-[10px] font-bold text-gray-400 bg-slate-100 px-3 py-1 rounded-full italic">Live monitoring enabled</span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left mt-4">
                            <thead>
                                <tr class="bg-slate-100 text-[10px] font-black uppercase text-slate-600">
                                    <th class="p-4">Username</th><th class="p-4">Status</th><th class="p-4">Email</th><th class="p-4">Account</th><th class="p-4">Role</th><th class="p-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="u in activeUsers" :key="u.username" class="border-b hover:bg-gray-50 transition-colors">
                                    <td class="p-4 font-bold">{{ u.username }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <div :class="isOnline(u) ? 'bg-green-500 animate-pulse' : 'bg-gray-300'" class="w-2 h-2 rounded-full"></div>
                                            <span :class="isOnline(u) ? 'text-green-600 font-bold' : 'text-gray-400'" class="text-xs uppercase">{{ isOnline(u) ? 'Online' : 'Offline' }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-slate-500">{{ u.email || 'N/A' }}</td>
                                    <td class="p-4">
                                        <span v-if="u.status === 'blocked'" class="text-red-600 text-[10px] font-black uppercase bg-red-50 px-2 py-1 rounded">Blocked</span>
                                        <span v-else class="text-green-600 text-[10px] font-black uppercase bg-green-50 px-2 py-1 rounded">Active</span>
                                    </td>
                                    <td class="p-4">
                                        <select v-if="u.username !== currentUser.username && u.role !== 'admin'" @change="changeUserRole(u.username, $event.target.value)" :value="u.role" class="p-2 border rounded text-xs font-bold bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                            <option value="user">Standard User</option><option value="moderator">Moderator</option>
                                        </select>
                                        <span v-else :class="u.role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700'" class="text-[10px] font-black uppercase px-2 py-1 rounded shadow-sm">{{ u.role }}</span>
                                    </td>
                                    <td class="p-4 flex gap-2">
                                        <template v-if="u.username !== currentUser.username && u.role !== 'admin'">
                                            <button @click="toggleUserBlock(u.username)" class="text-xs font-bold px-3 py-1 rounded border smooth-btn" :class="u.status === 'blocked' ? 'bg-green-600 text-white' : 'bg-orange-500 text-white'">{{ u.status === 'blocked' ? 'Unblock' : 'Block' }}</button>
                                            <button @click="deleteUser(u.username)" class="text-red-500 text-xs font-bold hover:underline transition-all">Delete</button>
                                        </template>
                                        <span v-else class="text-blue-500 text-[10px] font-black uppercase italic">Current Admin</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'add'" class="max-w-2xl mx-auto bg-white p-8 md:p-10 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 animate-pop hover-lift">
                <h2 class="text-2xl font-semibold mb-6 flex items-center gap-2 text-gray-900 tracking-tight"><i class="fas fa-plus-circle text-blue-500"></i> Register New Device</h2>
                <div v-if="duplicateAssetWarning" class="bg-red-50 text-red-600 border border-red-100 p-4 rounded-2xl mb-6 text-sm flex items-center gap-3 animate-pulse">
                    <i class="fas fa-exclamation-circle text-red-500 text-lg"></i> <span class="font-medium">Alert: An asset with this Tag or Serial Number already exists in the inventory.</span>
                </div>
                <div class="grid grid-cols-1 gap-5">
                    <div><label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider ml-1">Asset Tag</label><input v-model="singleForm.tag" type="text" placeholder="Ex: L-101" class="w-full bg-gray-50 border border-gray-200 p-3.5 rounded-xl mt-1.5 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth"></div>
                    <div><label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider ml-1">Device Type</label><input v-model="singleForm.type" type="text" placeholder="Laptop, Desktop, UPS etc." class="w-full bg-gray-50 border border-gray-200 p-3.5 rounded-xl mt-1.5 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth"></div>
                    <div class="grid grid-cols-2 gap-5">
                        <div><label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider ml-1">Brand</label><input v-model="singleForm.brand" type="text" placeholder="Ex: HP" class="w-full bg-gray-50 border border-gray-200 p-3.5 rounded-xl mt-1.5 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth"></div>
                        <div><label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider ml-1">Model</label><input v-model="singleForm.model" type="text" placeholder="Ex: ProBook 450" class="w-full bg-gray-50 border border-gray-200 p-3.5 rounded-xl mt-1.5 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth"></div>
                    </div>
                    <div><label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider ml-1">Serial Number</label><input v-model="singleForm.serial" type="text" placeholder="Manufacturer SN" class="w-full bg-gray-50 border border-gray-200 p-3.5 rounded-xl mt-1.5 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth"></div>
                    <div><label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider ml-1">Purchase Date</label><input v-model="singleForm.purchaseDate" type="date" class="w-full bg-gray-50 border border-gray-200 p-3.5 rounded-xl mt-1.5 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth text-gray-600"></div>
                    <button @click="addSingleDevice" class="w-full bg-blue-500 text-white font-semibold py-4 rounded-xl shadow-sm smooth-btn mt-4">Add Device to Inventory</button>
                </div>
            </div>

            <div v-if="activeTab === 'bulk' && ['admin', 'moderator'].includes(currentUser.role)" class="max-w-2xl mx-auto bg-white p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 animate-pop hover-lift relative overflow-hidden">
                <div v-if="isBulkProcessing" class="absolute inset-0 bg-white/80 backdrop-blur-md z-10 flex flex-col items-center justify-center rounded-3xl">
                    <i class="fas fa-circle-notch fa-spin text-5xl text-blue-500 mb-4"></i><h3 class="text-xl font-bold text-gray-800 tracking-tight">Smart System Verifying</h3><p class="text-sm font-medium text-gray-500 mt-2">Scanning inventory for duplicates...</p>
                </div>
                <div class="text-center mb-6"><i class="fas fa-file-csv text-5xl text-green-500 mb-4 hover:scale-110 transition-transform duration-300 drop-shadow-md"></i><h2 class="text-2xl font-bold text-gray-900 tracking-tight">Bulk Asset Upload</h2></div>
                <div class="bg-gray-50/80 p-6 rounded-2xl border-2 border-dashed border-gray-300 mb-2 transition-colors hover:bg-gray-100 hover:border-blue-400 cursor-pointer text-center relative group">
                    <p class="text-[11px] font-black text-gray-500 uppercase tracking-wider mb-4 group-hover:text-blue-500 transition-colors">Sequence: Tag, Type, Brand, Model, Serial, Date</p>
                    <input type="file" @change="handleFileUpload" accept=".csv" class="w-full text-sm outline-none file:mr-4 file:py-2.5 file:px-6 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all cursor-pointer">
                </div>
            </div>

            <div v-if="activeTab === 'profile'" class="max-w-md mx-auto space-y-6 animate-pop">
                <div class="bg-white p-8 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 hover-lift">
                    <h2 class="text-xl font-semibold mb-6 flex items-center gap-2 tracking-tight"><i class="fas fa-key text-blue-500"></i> Change Password</h2>
                    <div class="space-y-4">
                        <div><label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider ml-1">New Password</label><input v-model="profileUpdate.newPass" type="password" placeholder="Minimum 8 characters" class="w-full bg-gray-50 border border-gray-200 p-3.5 rounded-xl mt-1.5 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth"></div>
                        <div><label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider ml-1">Confirm New Password</label><input v-model="profileUpdate.confirmPass" type="password" placeholder="••••••••" class="w-full bg-gray-50 border border-gray-200 p-3.5 rounded-xl mt-1.5 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth"></div>
                        <button @click="changePassword" class="w-full bg-blue-500 text-white font-medium py-3.5 rounded-xl smooth-btn mt-2">Update Password</button>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-gray-100 hover-lift">
                    <h2 class="text-xl font-semibold mb-6 flex items-center gap-2 tracking-tight"><i class="fas fa-envelope text-blue-500"></i> Change Email Address</h2>
                    <div class="space-y-4">
                        <p class="text-xs font-medium text-gray-500 ml-1">Current: <span class="text-blue-600">{{ currentUser.email }}</span></p>
                        <div><label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider ml-1">New Email Address</label><input v-model="profileUpdate.newEmail" type="email" placeholder="new@gmail.com / @quantanite.com" class="w-full bg-gray-50 border border-gray-200 p-3.5 rounded-xl mt-1.5 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth"></div>
                        <div><label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider ml-1">Confirm New Email</label><input v-model="profileUpdate.confirmEmail" type="email" placeholder="confirm@gmail.com / @quantanite.com" class="w-full bg-gray-50 border border-gray-200 p-3.5 rounded-xl mt-1.5 outline-none focus:ring-2 focus:ring-blue-500/50 input-smooth"></div>
                        <button @click="changeEmail" class="w-full bg-gray-900 text-white font-medium py-3.5 rounded-xl smooth-btn mt-2">Update Email</button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    const { createApp } = Vue;
    createApp({
        data() {
            return {
                isInitialLoading: true, 
                currentUser: null, authView: 'login', authMessage: '', isWaitingApproval: false,
                messageColor: 'bg-red-50 text-red-600 border border-red-100',
                authForm: { username: '', password: '', confirmPassword: '', email: '' },
                forgotForm: { username: '', email: '', newPassword: '', confirmPassword: '' },
                profileUpdate: { newPass: '', confirmPass: '', newEmail: '', confirmEmail: '' },
                singleForm: { tag: '', type: '', brand: '', model: '', serial: '', purchaseDate: '' },
                
                users: [], activeTab: 'dashboard', searchQuery: '', statusFilter: '', assets: [], draftRepairs: {},
                
                // ADDED: Pagination Trackers
                currentPage: 1,
                itemsPerPage: 25,

                slots: [],
                slotForm: { sn: '', date_val: '', slotNo: '', slotName: '', totalAssets: '', returnToIT: '', eol: '', pending: '', remarks: '' },
                editingSlotId: null,
                
                reportFilterMonth: 'All',
                reportFilterYear: 'All',

                statusOptions: ['Received for Repair', 'In Assessment', 'Quick Repair Stage', 'Complex Stage', 'Ready', 'Irreparable', 'Delivered To IT', 'EOL/ Disposed', 'N/A'],
                
                heartbeatTimer: null, isBulkProcessing: false, bulkDuplicates: [], showBulkDuplicateModal: false
            }
        },
        async mounted() {
            await this.loadData();
            const sess = sessionStorage.getItem('activeUserV2');
            if(sess) {
                this.currentUser = JSON.parse(sess);
                this.startHeartbeat();
            }
            setTimeout(() => { this.isInitialLoading = false; }, 800);
        },
        // ADDED: Watchers to reset pagination when filtering
        watch: {
            searchQuery() { this.currentPage = 1; },
            statusFilter() { this.currentPage = 1; }
        },
        computed: {
            activeUsers() { return this.users.filter(u => u.status !== 'pending'); },
            pendingUsers() { return this.users.filter(u => u.status === 'pending'); },
            totalActiveUserCount() { return this.activeUsers.length; },
            
            inventoryAssets() { return this.assets.filter(a => a.status !== 'Pending Approval'); },
            pendingAssets() { return this.assets.filter(a => a.status === 'Pending Approval'); },

            filteredAssets() {
                let list = this.inventoryAssets;
                if(this.searchQuery.trim()) {
                    const q = this.searchQuery.toLowerCase();
                    return list.filter(a => a.tag.toLowerCase().includes(q) || a.serial.toLowerCase().includes(q));
                }
                if(this.statusFilter) list = list.filter(a => a.status === this.statusFilter);
                return list;
            },
            
            // ADDED: Client-Side Pagination Slicer
            totalPages() {
                return Math.ceil(this.filteredAssets.length / this.itemsPerPage) || 1;
            },
            paginatedAssets() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredAssets.slice(start, start + this.itemsPerPage);
            },

            inRepairCount() { return this.inventoryAssets.filter(a => ['Received for Repair', 'In Assessment', 'Quick Repair Stage', 'Complex Stage', 'Ready'].includes(a.status)).length; },
            
            deliveredCount() {
                return this.inventoryAssets.reduce((sum, asset) => sum + parseInt(asset.deliveryCount || 0, 10), 0);
            },
            
            statusCounts() { const c = {}; this.inventoryAssets.forEach(a => { c[a.status] = (c[a.status] || 0) + 1; }); return c; },
            allTimeRepairedCount() { return this.inventoryAssets.reduce((sum, asset) => sum + parseInt(asset.repairCount || 0, 10), 0); },
            duplicateAssetWarning() {
                if (!this.singleForm.tag && !this.singleForm.serial) return false;
                return this.assets.some(a => (this.singleForm.tag && a.tag.toLowerCase() === this.singleForm.tag.toLowerCase()) || (this.singleForm.serial && a.serial.toLowerCase() === this.singleForm.serial.toLowerCase()));
            },

            slotTotals() {
                return this.slots.reduce((acc, slot) => {
                    acc.totalAssets += parseInt(slot.totalAssets || 0, 10);
                    acc.returnToIT += parseInt(slot.returnToIT || 0, 10);
                    acc.eol += parseInt(slot.eol || 0, 10);
                    acc.pending += parseInt(slot.pending || 0, 10);
                    return acc;
                }, { totalAssets: 0, returnToIT: 0, eol: 0, pending: 0 });
            },
            
            monthlyReportData() {
                const dataMap = {};
                const mNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

                this.inventoryAssets.forEach(asset => {
                    if(asset.repairs && asset.repairs.length) {
                        const countedDatesForAsset = new Set(); 

                        asset.repairs.forEach(rep => {
                            const d = new Date(rep.date);
                            if (isNaN(d.getTime())) return; 

                            const y = d.getFullYear();
                            const m = d.getMonth() + 1;
                            const day = d.getDate();
                            const monthKey = `${y}-${m.toString().padStart(2, '0')}`;
                            
                            const dateKey = `${monthKey}-${day}`; 

                            if (!dataMap[monthKey]) {
                                dataMap[monthKey] = {
                                    key: monthKey,
                                    month: m,
                                    year: y,
                                    monthName: mNames[m - 1],
                                    assetCount: 0,
                                    cost: 0
                                };
                            }

                            dataMap[monthKey].cost += (parseFloat(rep.cost) || 0);

                            if (!countedDatesForAsset.has(dateKey)) {
                                dataMap[monthKey].assetCount += 1;
                                countedDatesForAsset.add(dateKey);
                            }
                        });
                    }
                });

                let list = Object.values(dataMap).sort((a, b) => b.key.localeCompare(a.key));

                if (this.reportFilterYear !== 'All') {
                    list = list.filter(item => item.year === parseInt(this.reportFilterYear));
                }
                if (this.reportFilterMonth !== 'All') {
                    list = list.filter(item => item.month === parseInt(this.reportFilterMonth));
                }

                return list;
            }
        },
        methods: {
            async loadData() {
                try {
                    const res = await fetch('api.php?action=load');
                    const data = await res.json();
                    if(data.success) {
                        if (data.users.length === 0) {
                            const defaultAdmin = { username: "admin", passwordHash: btoa("SuperAdmin#2026"), role: "admin", status: "active", email: "admin@assetcare.com", lastSeen: 0 };
                            this.users = [defaultAdmin];
                            this.saveDbUser(defaultAdmin);
                        } else { this.users = data.users; }
                        
                        this.assets = data.assets.map(a => {
                            if (a.status === 'Pending') a.status = 'In Assessment';
                            return a;
                        });

                        this.slots = data.slots || [];
                    }
                } catch (e) { console.error("API connection failed."); }
            },
            async saveDbUser(user) { await fetch('api.php?action=save_user', { method: 'POST', body: JSON.stringify(user) }); },
            async deleteDbUser(username) { await fetch('api.php?action=delete_user', { method: 'POST', body: JSON.stringify({username}) }); },
            async saveDbAsset(asset) { await fetch('api.php?action=save_asset', { method: 'POST', body: JSON.stringify(asset) }); },
            async deleteDbAsset(tag) { await fetch('api.php?action=delete_asset', { method: 'POST', body: JSON.stringify({tag}) }); },
            
            async saveDbSlot(slot) { 
                await fetch('api.php?action=save_slot', { method: 'POST', body: JSON.stringify(slot) }); 
                this.loadData(); 
            },
            async deleteDbSlot(id) { await fetch('api.php?action=delete_slot', { method: 'POST', body: JSON.stringify({id}) }); },
            submitSlot() {
                if (!this.slotForm.sn || !this.slotForm.slotNo) return alert('SN and Slot No are required!');
                this.saveDbSlot(this.slotForm);
                this.clearSlotForm();
            },
            clearSlotForm() {
                this.slotForm = { sn: '', date_val: '', slotNo: '', slotName: '', totalAssets: '', returnToIT: '', eol: '', pending: '', remarks: '' };
            },
            deleteSlot(id) {
                if(confirm('Are you sure you want to delete this slot record?')) {
                    this.slots = this.slots.filter(s => s.id !== id);
                    this.deleteDbSlot(id);
                }
            },
            
            editSlot(id) {
                this.editingSlotId = id;
            },
            cancelEdit() {
                this.editingSlotId = null;
                this.loadData(); 
            },
            saveSlotInline(slot) {
                this.saveDbSlot(slot);
                this.editingSlotId = null; 
            },

            startHeartbeat() { 
                this.updateHeartbeat(); 
                this.heartbeatTimer = setInterval(() => this.updateHeartbeat(), 2000); 
            },

            async updateHeartbeat() {
                if(!this.currentUser) return;
                const res = await fetch('api.php?action=load');
                const data = await res.json();
                
                if(data.success) {
                    this.users = data.users; 
                    
                    this.assets = data.assets.map(a => {
                        if (a.status === 'Pending') a.status = 'In Assessment';
                        return a;
                    });

                    if (this.editingSlotId === null) {
                        this.slots = data.slots || [];
                    }
                    
                    const myAccount = this.users.find(u => u.username === this.currentUser.username);
                    if (!myAccount) {
                        this.logout();
                        setTimeout(() => this.showMessage("Your account has been deleted by an administrator."), 500);
                        return; 
                    } else if (myAccount.status === 'blocked') {
                        this.logout();
                        setTimeout(() => this.showMessage("Your account has been blocked by an administrator."), 500);
                        return; 
                    } else if (myAccount.role !== this.currentUser.role) {
                        this.currentUser.role = myAccount.role;
                        sessionStorage.setItem('activeUserV2', JSON.stringify(this.currentUser));
                    }
                    myAccount.lastSeen = Date.now();
                    this.saveDbUser(myAccount);
                }
            },

            isOnline(user) { 
                if (!user.lastSeen) return false;
                const last = parseInt(user.lastSeen, 10);
                return Math.abs(Date.now() - last) < 60000; 
            },

            tabClass(t) { return this.activeTab === t ? 'bg-white text-blue-600 shadow-[0_2px_10px_rgba(0,0,0,0.04)] font-semibold border-transparent' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-200/50 font-medium border-transparent'; },
            
            statusBg(st) { const map = { 'In Assessment': 'bg-yellow-400', 'Quick Repair Stage': 'bg-blue-400', 'Complex Stage': 'bg-orange-400', 'Ready': 'bg-green-400', 'Irreparable': 'bg-red-400', 'EOL/ Disposed': 'bg-gray-500' }; return map[st] || 'bg-gray-300'; },
            statusColor(s) { 
                if (s === 'Ready') return 'bg-green-50 text-green-700 border-green-200';
                if (s === 'Irreparable') return 'bg-red-50 text-red-700 border-red-200';
                if (s === 'Delivered To IT') return 'bg-teal-50 text-teal-700 border-teal-200';
                if (s === 'EOL/ Disposed') return 'bg-gray-100 text-gray-700 border-gray-300';
                return 'bg-blue-50 text-blue-700 border-blue-200'; 
            },
            
            calculateTotalRepairCost(a) { return (a.repairs || []).reduce((s, i) => s + (parseFloat(i.cost) || 0), 0); },
            calculateAge(d) { if (!d || d === 'N/A') return "Unknown"; const b = new Date(d); const n = new Date(); let y = n.getFullYear() - b.getFullYear(); let m = n.getMonth() - b.getMonth(); if (m < 0) { y--; m += 12; } return `${y}Y, ${m}M`; },
            isValidEmail(email) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email); },
            showMessage(msg, type = 'error') { this.authMessage = msg; this.messageColor = type === 'success' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100'; setTimeout(() => { this.authMessage = ''; }, 5000); },

            verifyForgotIdentity() {
                if(!this.forgotForm.username || !this.forgotForm.email) return this.showMessage("Please provide both username and email.");
                const u = this.users.find(x => x.username === this.forgotForm.username && x.email === this.forgotForm.email);
                if(u) { this.authView = 'reset'; this.showMessage("Identity verified. Please set a new password.", "success"); } 
                else { this.showMessage("Verification failed. No matching user or email found."); }
            },
            resetPassword() {
                const { newPassword, confirmPassword, username } = this.forgotForm;
                if(!newPassword || newPassword.length < 8) return this.showMessage("Password must be at least 8 characters.");
                if(newPassword !== confirmPassword) return this.showMessage("Passwords do not match.");
                const userIdx = this.users.findIndex(u => u.username === username);
                if(userIdx !== -1) {
                    this.users[userIdx].passwordHash = btoa(newPassword);
                    this.saveDbUser(this.users[userIdx]);
                    this.authView = 'login'; this.forgotForm = { username: '', email: '', newPassword: '', confirmPassword: '' };
                    this.showMessage("Password updated successfully! You can now log in.", "success");
                } else { this.showMessage("Error updating password."); }
            },
            changePassword() {
                const { newPass, confirmPass } = this.profileUpdate;
                if (!newPass || newPass.length < 8) return alert("Password must be at least 8 characters long.");
                if (newPass !== confirmPass) return alert("Passwords do not match.");
                const userIdx = this.users.findIndex(u => u.username === this.currentUser.username);
                if (userIdx !== -1) {
                    const hash = btoa(newPass);
                    this.users[userIdx].passwordHash = hash;
                    this.saveDbUser(this.users[userIdx]);
                    this.currentUser.passwordHash = hash; sessionStorage.setItem('activeUserV2', JSON.stringify(this.currentUser));
                    this.profileUpdate.newPass = ''; this.profileUpdate.confirmPass = '';
                    alert("Password updated successfully across all systems!");
                }
            },
            changeEmail() {
                const { newEmail, confirmEmail } = this.profileUpdate;
                
                const validDomainRegex = /^[\w-\.]+@(gmail\.com|quantanite\.com)$/i;
                
                if (!newEmail || !validDomainRegex.test(newEmail)) {
                    return alert("Warning: Invalid domain! Only @gmail.com and @quantanite.com addresses are allowed.");
                }
                if (newEmail !== confirmEmail) {
                    return alert("Warning: The new email and confirm email addresses do not match!");
                }
                
                const userIdx = this.users.findIndex(u => u.username === this.currentUser.username);
                if (userIdx !== -1) {
                    this.users[userIdx].email = newEmail;
                    this.saveDbUser(this.users[userIdx]);
                    this.currentUser.email = newEmail; sessionStorage.setItem('activeUserV2', JSON.stringify(this.currentUser));
                    this.profileUpdate.newEmail = ''; this.profileUpdate.confirmEmail = '';
                    alert("Email updated successfully across all systems!");
                }
            },

            approveUser(username) { const u = this.users.find(x => x.username === username); if(u) { u.status = 'active'; this.saveDbUser(u); alert(`Access granted to ${username}.`); } },
            rejectUser(username) { if(confirm(`Reject ${username}?`)) { this.users = this.users.filter(x => x.username !== username); this.deleteDbUser(username); } },
            deleteUser(name) { if(confirm(`Delete ${name}?`)) { this.users = this.users.filter(u => u.username !== name); this.deleteDbUser(name); } },
            changeUserRole(username, newRole) { const u = this.users.find(x => x.username === username); if(u) { u.role = newRole; this.saveDbUser(u); } },
            toggleUserBlock(u) { const x = this.users.find(z => z.username === u); if(x) { x.status = x.status === 'blocked' ? 'active' : 'blocked'; this.saveDbUser(x); } },
            
            login() {
                if(!this.authForm.username || !this.authForm.password) return this.showMessage("Please fill all fields.");
                const hashedInput = btoa(this.authForm.password);
                const u = this.users.find(x => x.username === this.authForm.username && x.passwordHash === hashedInput);
                if(u) { 
                    if(u.status === 'pending') { this.isWaitingApproval = true; return; }
                    if(u.status === 'blocked') return this.showMessage("This account has been blocked by Admin.");
                    this.currentUser = u; sessionStorage.setItem('activeUserV2', JSON.stringify(u)); this.startHeartbeat();
                } else { this.showMessage("Invalid Username or Password."); }
            },

            register() { 
                if(!this.authForm.username || !this.authForm.password || !this.authForm.email) return this.showMessage("All fields are required.");
                
                const emailRegex = /^[\w-\.]+@(gmail\.com|quantanite\.com)$/i;
                if(!emailRegex.test(this.authForm.email)) return this.showMessage("Email must end with @gmail.com or @quantanite.com");
                
                if(this.authForm.password !== this.authForm.confirmPassword) return this.showMessage("Passwords do not match.");
                if(this.users.find(u => u.username === this.authForm.username)) return this.showMessage("Username already exists.");
                
                const newUser = { username: this.authForm.username, email: this.authForm.email, passwordHash: btoa(this.authForm.password), role: 'user', status: 'pending', requestDate: new Date().toLocaleString(), lastSeen: Date.now() };
                this.users.push(newUser); 
                this.saveDbUser(newUser); 
                
                this.authView = 'login'; this.authForm = { username: '', password: '', confirmPassword: '', email: '' };
                this.isWaitingApproval = true;
            },
            logout() { clearInterval(this.heartbeatTimer); this.currentUser = null; sessionStorage.removeItem('activeUserV2'); this.activeTab = 'dashboard'; },

            addSingleDevice() { 
                if(this.duplicateAssetWarning) return alert('Cannot add device: Tag or Serial number already exists.');
                const initialStatus = this.currentUser.role === 'user' ? 'Pending Approval' : 'N/A';
                
                const newAsset = { ...this.singleForm, status: initialStatus, repairs: [], repairCount: 0, deliveryCount: 0 };
                
                this.assets.push(newAsset); 
                this.saveDbAsset(newAsset); 
                this.singleForm = { tag: '', type: '', brand: '', model: '', serial: '', purchaseDate: '' }; 
                
                if (this.currentUser.role === 'user') {
                    alert("Asset submitted successfully! Waiting for Admin approval.");
                    this.activeTab = 'dashboard';
                } else {
                    this.activeTab = 'inventory'; 
                }
            },
            
            approveAsset(tag) {
                const a = this.assets.find(x => x.tag === tag);
                if(a) {
                    a.status = 'N/A';
                    this.saveDbAsset(a);
                    alert(`Asset ${tag} approved and added to inventory!`);
                }
            },
            rejectAsset(tag) {
                if(confirm(`Are you sure you want to reject and delete asset ${tag}?`)) {
                    this.assets = this.assets.filter(a => a.tag !== tag);
                    this.deleteDbAsset(tag);
                }
            },
            
            updateStatus(asset) { 
                if(asset.status === 'Ready') {
                    asset.repairCount = parseInt(asset.repairCount || 0, 10) + 1; 
                }
                
                if(asset.status === 'Delivered To IT') {
                    asset.deliveryCount = parseInt(asset.deliveryCount || 0, 10) + 1; 
                }
                
                if(this.saveDbAsset) this.saveDbAsset(asset);
                else if(this.saveDbAssets) this.saveDbAssets(); 
            },
            
            getDraft(tag) { 
                if(!this.draftRepairs[tag]) this.draftRepairs[tag] = {problem:'', repSerial:'', cost:0, date: ''}; 
                return this.draftRepairs[tag]; 
            },

            addRepair(tag) { 
                const a = this.assets.find(x => x.tag === tag); 
                const d = this.draftRepairs[tag]; 
                if(a && d && d.problem) { 
                    if(!a.repairs) a.repairs = []; 
                    
                    let finalDate;
                    if (d.date) {
                        const [y, m, day] = d.date.split('-');
                        finalDate = `${m}/${day}/${y}`;
                    } else {
                        finalDate = new Date().toLocaleDateString();
                    }

                    a.repairs.push({...d, date: finalDate}); 
                    this.draftRepairs[tag] = {problem:'', repSerial:'', cost:0, date: ''}; 
                    if(this.saveDbAsset) this.saveDbAsset(a);
                    else if(this.saveDbAssets) this.saveDbAssets();
                } 
            },
            
            deleteAsset(tag) { if(confirm('Delete?')) { this.assets = this.assets.filter(a => a.tag !== tag); this.deleteDbAsset(tag); } },

            handleFileUpload(event) {
                const file = event.target.files[0]; if (!file) return;
                this.isBulkProcessing = true; this.bulkDuplicates = [];
                const reader = new FileReader();
                reader.onload = (e) => {
                    const rows = e.target.result.split('\n');
                    const validNewAssets = []; const duplicateEntries = [];
                    setTimeout(() => {
                        rows.forEach((row, i) => { 
                            if(i > 0 && row.trim()) { 
                                const c = row.split(',').map(x => x.trim()); 
                                const tag = c[0]; const type = c[1]; const brand = c[2]; const model = c[3]; const serial = c[4]; const purchaseDate = c[5];
                                const isDuplicate = this.assets.some(a => (tag && a.tag.toLowerCase() === tag.toLowerCase()) || (serial && a.serial.toLowerCase() === serial.toLowerCase())) || validNewAssets.some(a => (tag && a.tag.toLowerCase() === tag.toLowerCase()) || (serial && a.serial.toLowerCase() === serial.toLowerCase()));
                                
                                const newAsset = { tag, type, brand, model, serial, purchaseDate, status: 'N/A', repairs: [], repairCount: 0, deliveryCount: 0 };
                                
                                if (isDuplicate) { duplicateEntries.push(newAsset); } else { validNewAssets.push(newAsset); }
                            } 
                        });
                        if (validNewAssets.length > 0) { 
                            this.assets.push(...validNewAssets); 
                            validNewAssets.forEach(a => this.saveDbAsset(a)); 
                        }
                        this.isBulkProcessing = false; event.target.value = '';
                        if (duplicateEntries.length > 0) { this.bulkDuplicates = duplicateEntries; this.showBulkDuplicateModal = true; } 
                        else if (validNewAssets.length > 0) { this.activeTab = 'inventory'; }
                    }, 2000); 
                }; reader.readAsText(file);
            },
            closeDuplicateModal() { this.showBulkDuplicateModal = false; this.activeTab = 'inventory'; },
            
            exportCSV() {
                if (!this.inventoryAssets.length) return alert("No data to export.");
                const headers = "Asset Tag,Type,Brand,Model,Serial Number,Status,Purchase Date,Repair Count\n";
                const rows = this.inventoryAssets.map(a => `${a.tag},${a.type},${a.brand},${a.model},${a.serial},${a.status},${a.purchaseDate || 'N/A'},${a.repairCount || 0}`).join("\n");
                const blob = new Blob([headers + rows], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob); const a = document.createElement('a');
                a.href = url; a.download = `AssetCare_Export_${new Date().toISOString().split('T')[0]}.csv`; a.click();
            },
            exportPDF() {
                if (!this.inventoryAssets.length) return alert("No data to export.");
                const { jsPDF } = window.jspdf; const doc = new jsPDF();
                doc.setFontSize(18); doc.text("AssetCare Inventory Report", 14, 15);
                const tableColumn = ["Tag", "Type", "Brand", "Model", "Serial", "Status"];
                const tableRows = this.inventoryAssets.map(a => [a.tag, a.type, a.brand, a.model, a.serial, a.status]);
                doc.autoTable({ head: [tableColumn], body: tableRows, startY: 28 });
                doc.save(`AssetCare_Report_${new Date().toISOString().split('T')[0]}.pdf`);
            }
        }
    }).mount('#app')
</script>
</body>
</html>