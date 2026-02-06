<template>
    <div
        class="min-h-screen bg-gradient-to-br from-gray-900 via-black to-gray-900 p-4 md:p-8"
    >
        <!-- Background Particles Effect -->
        <div class="particles-container fixed inset-0 z-0">
            <div
                v-for="i in 30"
                :key="i"
                :class="`particle particle-${i}`"
                :style="`left: ${Math.random() * 100}%; top: ${Math.random() * 100}%;`"
            ></div>
        </div>

        <div class="max-w-7xl mx-auto relative z-10">
            <!-- Animated Header -->
            <div class="mb-12 text-center">
                <h1 class="text-5xl md:text-6xl font-bold mb-4">
                    <span
                        class="bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent animate-gradient"
                    >
                        User Profile
                    </span>
                </h1>
                <p class="text-gray-400 text-lg">
                    Craft your digital identity with precision
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Form (2/3) -->
                <div class="lg:col-span-2">
                    <!-- Glassmorphism Form Card -->
                    <div class="glass-card p-8 rounded-3xl">
                        <div class="flex items-center justify-between mb-10">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <div
                                        class="absolute -inset-1 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full blur opacity-70 animate-pulse-slow"
                                    ></div>
                                    <div
                                        class="relative p-3 bg-gray-800 rounded-full"
                                    >
                                        <svg
                                            class="w-8 h-8 text-blue-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white">
                                        Edit Profile
                                    </h2>
                                    <p class="text-gray-400">
                                        Update your personal information
                                    </p>
                                </div>
                            </div>

                            <!-- Save Button with Animation -->
                            <button
                                @click="saveProfile"
                                @mouseenter="saveHover = true"
                                @mouseleave="saveHover = false"
                                class="group relative overflow-hidden px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-2xl hover:shadow-blue-500/30 transition-all duration-500 transform hover:-translate-y-1"
                            >
                                <span
                                    class="relative z-10 flex items-center gap-2"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        :class="{ 'animate-spin': saving }"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                    {{ saving ? "Saving..." : "Save Changes" }}
                                </span>
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-blue-700 to-purple-700 transform translate-x-full group-hover:translate-x-0 transition-transform duration-500"
                                ></div>
                            </button>
                        </div>

                        <!-- Form Grid with Floating Labels -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Company Field -->
                            <div class="md:col-span-2">
                                <div class="floating-input">
                                    <input
                                        type="text"
                                        v-model="form.company"
                                        disabled
                                        class="input-field peer"
                                        placeholder=" "
                                    />
                                    <label class="floating-label"
                                        >Company</label
                                    >
                                    <div
                                        class="absolute right-4 top-1/2 transform -translate-y-1/2"
                                    >
                                        <span
                                            class="text-xs px-3 py-1 bg-gray-700/50 text-gray-400 rounded-full"
                                            >Locked</span
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Username -->
                            <div>
                                <div class="floating-input">
                                    <input
                                        type="text"
                                        v-model="form.username"
                                        class="input-field peer"
                                        placeholder=" "
                                    />
                                    <label class="floating-label"
                                        >Username</label
                                    >
                                    <div
                                        class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500"
                                    >
                                        @
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <div class="floating-input">
                                    <input
                                        type="email"
                                        v-model="form.email"
                                        class="input-field peer"
                                        placeholder=" "
                                    />
                                    <label class="floating-label"
                                        >Email Address</label
                                    >
                                    <div
                                        class="absolute left-4 top-1/2 transform -translate-y-1/2"
                                    >
                                        <svg
                                            class="w-5 h-5 text-gray-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Name Fields -->
                            <div>
                                <div class="floating-input">
                                    <input
                                        type="text"
                                        v-model="form.firstName"
                                        class="input-field peer"
                                        placeholder=" "
                                    />
                                    <label class="floating-label"
                                        >First Name</label
                                    >
                                </div>
                            </div>

                            <div>
                                <div class="floating-input">
                                    <input
                                        type="text"
                                        v-model="form.lastName"
                                        class="input-field peer"
                                        placeholder=" "
                                    />
                                    <label class="floating-label"
                                        >Last Name</label
                                    >
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <div class="floating-input">
                                    <input
                                        type="text"
                                        v-model="form.address"
                                        class="input-field peer"
                                        placeholder=" "
                                    />
                                    <label class="floating-label"
                                        >Address</label
                                    >
                                    <div
                                        class="absolute left-4 top-1/2 transform -translate-y-1/2"
                                    >
                                        <svg
                                            class="w-5 h-5 text-gray-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Fields -->
                            <div>
                                <div class="floating-input">
                                    <input
                                        type="text"
                                        v-model="form.city"
                                        class="input-field peer"
                                        placeholder=" "
                                    />
                                    <label class="floating-label">City</label>
                                </div>
                            </div>

                            <div>
                                <div class="floating-input">
                                    <select
                                        v-model="form.country"
                                        class="input-field peer appearance-none"
                                    >
                                        <option value="">Select Country</option>
                                        <option value="US">
                                            United States
                                        </option>
                                        <option value="UK">
                                            United Kingdom
                                        </option>
                                        <option value="JP">Japan</option>
                                        <option value="DE">Germany</option>
                                        <option value="SG">Singapore</option>
                                    </select>
                                    <label class="floating-label"
                                        >Country</label
                                    >
                                    <div
                                        class="absolute right-4 top-1/2 transform -translate-y-1/2"
                                    >
                                        <svg
                                            class="w-5 h-5 text-gray-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 9l-7 7-7-7"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="floating-input">
                                    <input
                                        type="text"
                                        v-model="form.postalCode"
                                        class="input-field peer"
                                        placeholder=" "
                                    />
                                    <label class="floating-label"
                                        >Postal Code</label
                                    >
                                </div>
                            </div>

                            <!-- About Me -->
                            <div class="md:col-span-2">
                                <div class="relative">
                                    <textarea
                                        v-model="form.aboutMe"
                                        rows="4"
                                        class="input-field peer pt-6 resize-none"
                                        placeholder=" "
                                    ></textarea>
                                    <label class="floating-label"
                                        >About Me</label
                                    >
                                    <div
                                        class="absolute right-4 bottom-4 text-sm text-gray-500"
                                    >
                                        <span
                                            :class="{
                                                'text-green-400':
                                                    form.aboutMe.length <= 200,
                                                'text-red-400':
                                                    form.aboutMe.length > 200,
                                            }"
                                        >
                                            {{ form.aboutMe.length }}/200
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Search Section -->
                        <div class="mt-12 pt-8 border-t border-gray-700/50">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="p-2 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-xl"
                                    >
                                        <svg
                                            class="w-6 h-6 text-blue-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3
                                            class="text-xl font-bold text-white"
                                        >
                                            Advanced Search
                                        </h3>
                                        <p class="text-gray-400">
                                            Find connections and content
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button
                                        class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg transition-colors duration-300"
                                    >
                                        Filters
                                    </button>
                                    <button
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-300"
                                    >
                                        Search
                                    </button>
                                </div>
                            </div>

                            <div class="relative group">
                                <div
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 group-focus-within:text-blue-400 transition-colors duration-300"
                                >
                                    <svg
                                        class="w-6 h-6"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                        />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    v-model="searchQuery"
                                    @input="handleSearch"
                                    class="w-full pl-14 pr-40 py-4 bg-gray-800/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-300"
                                    placeholder="Search across profiles, connections, and posts..."
                                />
                                <div
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 flex items-center gap-3"
                                >
                                    <kbd
                                        class="px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded border border-gray-600"
                                        >⌘</kbd
                                    >
                                    <kbd
                                        class="px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded border border-gray-600"
                                        >K</kbd
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Profile Preview (1/3) -->
                <div class="space-y-8">
                    <!-- Profile Card -->
                    <div class="glass-card p-8 rounded-3xl">
                        <!-- Avatar with Glow Effect -->
                        <div class="relative mb-8">
                            <div
                                class="absolute -inset-1 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-full blur-2xl opacity-50 animate-gradient-slow"
                            ></div>
                            <div class="relative">
                                <div
                                    class="w-32 h-32 mx-auto bg-gradient-to-br from-gray-800 to-gray-900 rounded-full border-4 border-gray-800 overflow-hidden"
                                >
                                    <div
                                        class="w-full h-full flex items-center justify-center"
                                    >
                                        <span
                                            class="text-4xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent"
                                        >
                                            {{ getInitials() }}
                                        </span>
                                    </div>
                                </div>
                                <!-- Online Status -->
                                <div
                                    class="absolute bottom-4 right-4 w-6 h-6 bg-green-400 rounded-full border-4 border-gray-900"
                                >
                                    <div
                                        class="w-full h-full bg-green-400 rounded-full animate-ping opacity-75"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Info -->
                        <div class="text-center mb-8">
                            <h3 class="text-2xl font-bold text-white mb-2">
                                {{ form.firstName }} {{ form.lastName }}
                            </h3>
                            <p class="text-gray-400 mb-4">
                                @{{ form.username }}
                            </p>
                            <div class="flex items-center justify-center gap-3">
                                <span
                                    class="px-4 py-1 bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-blue-400 rounded-full text-sm font-medium border border-blue-500/30"
                                >
                                    Premium Member
                                </span>
                                <span
                                    class="px-4 py-1 bg-gradient-to-r from-green-500/20 to-emerald-500/20 text-green-400 rounded-full text-sm font-medium border border-green-500/30"
                                >
                                    Verified
                                </span>
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-3 gap-4 mb-8">
                            <div
                                class="text-center p-4 bg-gray-800/30 rounded-xl hover:bg-gray-800/50 transition-all duration-300 group cursor-pointer"
                            >
                                <div class="text-2xl font-bold text-white mb-1">
                                    89
                                </div>
                                <div
                                    class="text-xs text-gray-400 group-hover:text-blue-400 transition-colors"
                                >
                                    Projects
                                </div>
                            </div>
                            <div
                                class="text-center p-4 bg-gray-800/30 rounded-xl hover:bg-gray-800/50 transition-all duration-300 group cursor-pointer"
                            >
                                <div class="text-2xl font-bold text-white mb-1">
                                    2.4K
                                </div>
                                <div
                                    class="text-xs text-gray-400 group-hover:text-purple-400 transition-colors"
                                >
                                    Followers
                                </div>
                            </div>
                            <div
                                class="text-center p-4 bg-gray-800/30 rounded-xl hover:bg-gray-800/50 transition-all duration-300 group cursor-pointer"
                            >
                                <div class="text-2xl font-bold text-white mb-1">
                                    312
                                </div>
                                <div
                                    class="text-xs text-gray-400 group-hover:text-pink-400 transition-colors"
                                >
                                    Following
                                </div>
                            </div>
                        </div>

                        <!-- Quote Card -->
                        <div class="relative mb-8">
                            <div
                                class="absolute -inset-1 bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-2xl blur-xl"
                            ></div>
                            <div
                                class="relative p-6 bg-gradient-to-br from-gray-800/80 to-gray-900/80 rounded-2xl border border-gray-700/50"
                            >
                                <div class="flex items-start gap-4">
                                    <div
                                        class="p-3 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-xl"
                                    >
                                        <svg
                                            class="w-6 h-6 text-blue-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <p
                                            class="text-gray-300 italic leading-relaxed"
                                        >
                                            "Lamborghini Mercy<br />
                                            Your chick she so thirsty<br />
                                            I'm in that two seat Lambo"
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social Connections -->
                        <div class="mb-8">
                            <h4 class="text-lg font-semibold text-white mb-4">
                                Connect
                            </h4>
                            <div class="flex gap-3">
                                <button
                                    v-for="(social, index) in socials"
                                    :key="index"
                                    @click="connectSocial(social.name)"
                                    class="social-button group"
                                    :class="`hover:${social.hoverColor}`"
                                >
                                    <component
                                        :is="social.icon"
                                        class="w-5 h-5 transition-transform duration-300 group-hover:scale-110"
                                    />
                                </button>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <button
                            class="w-full py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-2xl hover:shadow-blue-500/30 transition-all duration-500 transform hover:-translate-y-1"
                        >
                            Send Message
                        </button>
                    </div>

                    <!-- Activity Card -->
                    <div class="glass-card p-6 rounded-3xl">
                        <h4 class="text-lg font-semibold text-white mb-4">
                            Recent Activity
                        </h4>
                        <div class="space-y-4">
                            <div
                                v-for="activity in recentActivities"
                                :key="activity.id"
                                class="activity-item group"
                            >
                                <div
                                    class="flex items-center gap-4 p-3 hover:bg-gray-800/30 rounded-xl transition-all duration-300 cursor-pointer"
                                >
                                    <div class="relative">
                                        <div
                                            class="absolute -inset-1 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-full blur-sm"
                                        ></div>
                                        <div
                                            :class="`p-3 ${activity.bgColor} rounded-xl`"
                                        >
                                            <component
                                                :is="activity.icon"
                                                class="w-5 h-5 text-white"
                                            />
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p
                                            class="text-sm font-medium text-white"
                                        >
                                            {{ activity.title }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ activity.time }}
                                        </p>
                                    </div>
                                    <svg
                                        class="w-5 h-5 text-gray-500 group-hover:text-blue-400 transition-colors duration-300"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5l7 7-7 7"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-12 text-center text-gray-500 text-sm">
                <p>© 2024 Creative Code Inc. All rights reserved.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";

// Componentes SVG para iconos
const SocialIcon = {
    template: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>`,
};

const TwitterIcon = {
    template: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.213c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>`,
};

const LinkedinIcon = {
    template: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>`,
};

const InstagramIcon = {
    template: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>`,
};

// Estado reactivo
const form = ref({
    company: "Creative Code Inc.",
    username: "michael23",
    email: "michael@creativecode.com",
    firstName: "Mike",
    lastName: "Andrew",
    address: "Bld Mihail Kogalniceanu, nr. 8 Bl 1, Sc 1, Ap 09",
    city: "New York",
    country: "US",
    postalCode: "10001",
    aboutMe:
        "Lamborghini Mercy, Your chick she so thirsty, I'm in that two seat Lambo.",
});

const searchQuery = ref("");
const saving = ref(false);
const saveHover = ref(false);

// Social media data
const socials = ref([
    { name: "facebook", icon: SocialIcon, hoverColor: "hover:bg-blue-500" },
    { name: "twitter", icon: TwitterIcon, hoverColor: "hover:bg-sky-500" },
    { name: "linkedin", icon: LinkedinIcon, hoverColor: "hover:bg-blue-700" },
    { name: "instagram", icon: InstagramIcon, hoverColor: "hover:bg-pink-600" },
]);

// Recent activities
const recentActivities = ref([
    {
        id: 1,
        title: "Profile picture updated",
        time: "2 hours ago",
        bgColor: "bg-blue-500/20",
        icon: "div",
    },
    {
        id: 2,
        title: "Email verified",
        time: "1 day ago",
        bgColor: "bg-green-500/20",
        icon: "div",
    },
    {
        id: 3,
        title: "Connected to Twitter",
        time: "3 days ago",
        bgColor: "bg-sky-500/20",
        icon: "div",
    },
]);

// Métodos
const getInitials = () => {
    return (form.value.firstName?.[0] || "") + (form.value.lastName?.[0] || "");
};

const saveProfile = () => {
    saving.value = true;
    setTimeout(() => {
        saving.value = false;
        console.log("Profile saved:", form.value);
    }, 1500);
};

const handleSearch = () => {
    console.log("Searching:", searchQuery.value);
};

const connectSocial = (platform) => {
    console.log(`Connecting to ${platform}`);
};
</script>

<style scoped>
/* Animaciones personalizadas */
@keyframes gradient {
    0%,
    100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

@keyframes pulse-slow {
    0%,
    100% {
        opacity: 0.7;
    }
    50% {
        opacity: 0.4;
    }
}

@keyframes float {
    0%,
    100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradient 3s ease infinite;
}

.animate-gradient-slow {
    background-size: 200% 200%;
    animation: gradient 6s ease infinite;
}

.animate-pulse-slow {
    animation: pulse-slow 3s ease-in-out infinite;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

/* Glassmorphism effect */
.glass-card {
    background: rgba(17, 25, 40, 0.75);
    backdrop-filter: blur(16px) saturate(180%);
    -webkit-backdrop-filter: blur(16px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.125);
}

/* Floating label inputs */
.floating-input {
    position: relative;
}

.input-field {
    @apply w-full px-4 py-4 bg-gray-800/30 border border-gray-700 rounded-xl text-white placeholder-transparent focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-300;
}

.floating-label {
    @apply absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 transition-all duration-300 pointer-events-none;
}

.input-field:focus + .floating-label,
.input-field:not(:placeholder-shown) + .floating-label {
    @apply -translate-y-8 text-sm text-blue-400;
}

/* Social buttons */
.social-button {
    @apply flex-1 p-4 bg-gray-800/30 rounded-xl border border-gray-700/50 hover:border-transparent transition-all duration-300 transform hover:-translate-y-1;
}

/* Activity items */
.activity-item:hover {
    transform: translateX(4px);
    transition: transform 0.3s ease;
}

/* Particles background */
.particles-container {
    overflow: hidden;
}

.particle {
    position: absolute;
    width: 2px;
    height: 2px;
    background: rgba(59, 130, 246, 0.5);
    border-radius: 50%;
    animation: float 15s infinite ease-in-out;
}

.particle:nth-child(odd) {
    background: rgba(139, 92, 246, 0.5);
    animation-delay: -5s;
}

.particle:nth-child(3n) {
    background: rgba(236, 72, 153, 0.5);
    animation-delay: -10s;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    @apply bg-gray-900;
}

::-webkit-scrollbar-thumb {
    @apply bg-gradient-to-b from-blue-500 to-purple-500 rounded-full;
}

::-webkit-scrollbar-thumb:hover {
    @apply from-blue-600 to-purple-600;
}

/* Selection color */
::selection {
    @apply bg-blue-500/30 text-white;
}
</style>
