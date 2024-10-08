<header class="header border-b">
    <div class="flex justify-between w-full">
        <div class="w-full p-5">
            <h2 class=" text-[#785839] font-semibold tracking-tighter lg:text-2xl text-sm">Sistem Informasi Pelaporan
                Indikator Pelayanan Rawat Inap</h2>
        </div>
        <div class="w-full flex justify-end">
            <div class="dropdown border-l p-5 hover:bg-neutral-100">
                <button class="dropdown-account-toggle mt-1 "><span
                        class="flex gap-5 mt-0 font-semibold text-gray-600"><span>{{ auth()->user()->name }}</span>
                        <iconify-icon icon="iconamoon:arrow-down-2-duotone" class="text-2xl"></iconify-icon>
                    </span></button>
                <div class="dropdown-menu hidden">
                    <div class="avatar-account flex gap-1 border-b p-2">
                        <div class="avatar-profile p-3">
                            <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }} Muktiwati" class="w-12 rounded-full"
                                alt="" srcset="">
                        </div>
                        <div class="avatar">
                            <h2 class="font-bold">{{ auth()->user()->name }}</h2>
                            <p class="text-sm"></p>
                        </div>
                    </div>
                    <ul class="dropdown-menu-item">
                        <li>
                            <a href="#">
                                <button class="item-dropdown">Profile</button>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <button class="item-dropdown">Reset Passowrd</button>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}" id="btn-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <button class="item-dropdown">Logout</button>
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
