<?php
// File: header.php (or wherever the navbar is defined)
?>
<div class="bg-gradient-to-br from-black via-red-950 to-black text-white shadow-lg border-b border-red-800">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <ul class="flex space-x-8 pt-3">
                    <li>
                        <a href="/" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white hover:text-red-600 hover:border-b-2 hover:border-red-600 transition-all duration-150">
                            Sākums
                        </a>
                    </li>
                    <?php if (auth()): ?>
                        <?php $role = $_SESSION['user']['role'];  ?>
                        <?php if ($role === 'admin'): ?>
                            <li>
                                <a href="/classes" class="inline-flex items-center px-3 py-2 text-sm font-medium hover:text-blue-600 hover:border-b-2 hover:border-blue-600 transition-all duration-150">
                                    Classes
                                </a>
                            </li>
                            <li>
                                <a href="/lessons" class="inline-flex items-center px-3 py-2 text-sm font-medium hover:text-blue-600 hover:border-b-2 hover:border-blue-600 transition-all duration-150">
                                    Lessons
                                </a>
                            </li>
                            <li>
                                <a href="/students" class="inline-flex items-center px-3 py-2 text-sm font-medium hover:text-blue-600 hover:border-b-2 hover:border-blue-600 transition-all duration-150">
                                    Users
                                </a>
                            </li>
                        <?php elseif ($role === 'teacher'): ?>
                            <li>
                                <a href="/teacher/classes" class="inline-flex items-center px-3 py-2 text-sm font-medium hover:text-blue-600 hover:border-b-2 hover:border-blue-600 transition-all duration-150">
                                    My Classes
                                </a>
                            </li>
                            <li>
                                <a href="/grades" class="inline-flex items-center px-3 py-2 text-sm font-medium hover:text-blue-600 hover:border-b-2 hover:border-blue-600 transition-all duration-150">
                                    Manage Grades
                                </a>
                            </li>
                            <li>
                                <a href="/detentions/create" class="inline-flex items-center px-3 py-2 text-sm font-medium hover:text-blue-600 hover:border-b-2 hover:border-blue-600 transition-all duration-150">
                                    Assign Detentions
                                </a>
                            </li>
                            <li>
                                <a href="/mail" class="inline-flex items-center px-3 py-2 text-sm font-medium hover:text-blue-600 hover:border-b-2 hover:border-blue-600 transition-all duration-150">
                                    Mail
                                </a>
                            </li>
                        <?php elseif ($role === 'student'): ?>
                            <li>
                                <a href="/grades" class="inline-flex items-center px-3 py-2 text-sm font-medium hover:text-blue-600 hover:border-b-2 hover:border-blue-600 transition-all duration-150">
                                    My Grades
                                </a>
                            </li>
                            <li>
                                <a href="/diary" class="inline-flex items-center px-3 py-2 text-sm font-medium hover:text-blue-600 hover:border-b-2 hover:border-blue-600 transition-all duration-150">
                                    My Diary
                                </a>
                            </li>
                            <li>
                                <a href="/mail" class="inline-flex items-center px-3 py-2 text-sm font-medium hover:text-blue-600 hover:border-b-2 hover:border-blue-600 transition-all duration-150">
                                    Mail
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="flex">
                <?php if (!auth()): ?>
                    <ul class="flex space-x-4 pt-3">
                        <li>
                            <a href="/login" class="inline-flex bg-gradient-to-br from-gray-800 to-red-500 rounded-l-3xl px-8 items-center py-2 text-sm font-bold text-gray-100 hover:to-gray-500 hover:from-red-600 transition hover:scale-105 duration-400">
                                Login
                            </a>
                        </li>
                        <li>
                            <a href="/register" class="inline-flex items-center px-6 py-2 text-sm font-bold rounded-r-3xl text-red-700 hover:text-red-600 transition-all border-red-500 duration-400 border hover:scale-105 border-lined">
                                Register
                            </a>
                        </li>
                    </ul>
                <?php else: ?>
                    <?php if (isset($_SESSION['user']['image'])): ?>
                        <img src="<?= htmlspecialchars($_SESSION['user']['image'], ENT_QUOTES, 'UTF-8') ?>" alt="Profile" class="w-10 h-10 rounded-full mt-2.5 mr-3 border border-white">
                    <?php endif; ?>
                    <ul class="flex space-x-4 pt-3">
                        <li>
                            <a href="/logout" class="inline-flex bg-gradient-to-br from-gray-500 to-red-500 rounded-l-3xl px-8 items-center py-2 text-sm font-bold text-gray-100 hover:to-gray-500 hover:from-red-600 transition hover:scale-105 duration-400">
                                Logout
                            </a>
                        </li>
                        <li>
                            <a href="/profile" class="inline-flex items-center px-6 py-2 text-sm font-bold rounded-r-3xl text-red-700 hover:text-red-500 transition-all duration-400 border hover:scale-105 border-dashed">
                                Profile
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>