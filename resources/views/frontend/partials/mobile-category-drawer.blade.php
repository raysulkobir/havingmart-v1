{{-- Mobile Category Menu Drawer - Optimized: uses pre-cached categories --}}
@php
    // Single cached query for all categories with translations
    $allCategories = Cache::remember('all_nav_categories', 3600, function () {
        return \App\Models\Category::where('status', 1)
            ->with(['category_translations'])
            ->orderBy('order_level', 'desc')
            ->get();
    });
    $level0 = $allCategories->where('level', 0)->take(15);
@endphp

<!-- Mobile Category Menu Overlay -->
<div class="mobile-category-overlay" id="mobileCategoryOverlay" onclick="closeMobileCategoryMenu()"></div>

<!-- Mobile Category Menu Drawer -->
<div class="mobile-category-drawer" id="mobileCategoryDrawer">
    <div class="mobile-menu-header">
        <h3 class="mobile-menu-title">Menu</h3>
        <button class="mobile-menu-close" onclick="closeMobileCategoryMenu()">
            <i class="las la-times"></i>
        </button>
    </div>

    <div class="mobile-menu-body">
        <!-- My Account Section -->
        @auth
            <a href="{{ route('dashboard') }}" class="mobile-account-link">
                <div class="mobile-account-icon">
                    @if(Auth::user()->avatar_original != null)
                        <img src="{{ uploaded_asset(Auth::user()->avatar_original) }}" alt="{{ Auth::user()->name }}">
                    @else
                        <i class="las la-user"></i>
                    @endif
                </div>
                <span>My Account</span>
            </a>
        @else
            <a href="{{ route('user.login') }}" class="mobile-account-link">
                <div class="mobile-account-icon">
                    <i class="las la-user"></i>
                </div>
                <span>Login</span>
            </a>
        @endauth

        <!-- Categories -->
        <div class="mobile-categories">
        @foreach ($level0 as $category)
            @php
                $childCategories = $allCategories->where('parent_id', $category->id);
                $hasChildren = $childCategories->count() > 0;
            @endphp
                
                <div class="mobile-category-item">
                    <div class="mobile-category-header">
                        <a href="{{ route('products.category', $category->slug) }}" class="mobile-category-main">
                            <img
                                class="mobile-cat-icon lazyload"
                                src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                data-src="{{ uploaded_asset($category->icon) }}"
                                alt="{{ $category->getTranslation('name') }}"
                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                            >
                            <span class="mobile-cat-name">{{ $category->getTranslation('name') }}</span>
                        </a>
                        
                        @if($hasChildren)
                            <button class="mobile-category-toggle" 
                                    id="toggle-{{ $category->id }}"
                                    onclick="toggleMobileCategory({{ $category->id }})">
                                <i class="las la-plus"></i>
                            </button>
                        @endif
                    </div>
                    
                    @if($hasChildren)
                        <div class="mobile-subcategory-list" id="subcategory-{{ $category->id }}" style="display: none;">
                            @foreach($childCategories as $subCategory)
                                @php
                                    $subChildCategories = $allCategories->where('parent_id', $subCategory->id);
                                    $hasSubChildren = $subChildCategories->count() > 0;
                                @endphp
                                
                                <div class="mobile-subcategory-wrapper">
                                    <div class="mobile-subcategory-header">
                                        <a href="{{ route('products.category', $subCategory->slug) }}" class="mobile-subcategory-link">
                                            {{ $subCategory->getTranslation('name') }}
                                        </a>
                                        
                                        @if($hasSubChildren)
                                            <button class="mobile-subcategory-toggle" 
                                                    id="subcat-toggle-{{ $subCategory->id }}"
                                                    onclick="toggleSubcategoryItems({{ $subCategory->id }})">
                                                <i class="las la-plus"></i>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    @if($hasSubChildren)
                                        <div class="mobile-subcat-items" id="subcat-items-{{ $subCategory->id }}" style="display: none;">
                                            @foreach($subChildCategories as $item)
                                                <a href="{{ route('products.category', $item->slug) }}" class="mobile-subcat-item">
                                                    {{ $item->getTranslation('name') }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
function openMobileCategoryMenu() {
    document.getElementById('mobileCategoryDrawer').classList.add('active');
    document.getElementById('mobileCategoryOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeMobileCategoryMenu() {
    document.getElementById('mobileCategoryDrawer').classList.remove('active');
    document.getElementById('mobileCategoryOverlay').classList.remove('active');
    document.body.style.overflow = '';
}
function toggleMobileCategory(categoryId) {
    const subcategoryList = document.getElementById('subcategory-' + categoryId);
    const toggleBtn = document.getElementById('toggle-' + categoryId);
    document.querySelectorAll('.mobile-subcategory-list').forEach(list => {
        if (list.id !== 'subcategory-' + categoryId) { list.style.display = 'none'; }
    });
    document.querySelectorAll('.mobile-category-toggle').forEach(btn => {
        if (btn.id !== 'toggle-' + categoryId) { btn.classList.remove('active'); }
    });
    if (subcategoryList.style.display === 'none' || subcategoryList.style.display === '') {
        subcategoryList.style.display = 'block';
        toggleBtn.classList.add('active');
    } else {
        subcategoryList.style.display = 'none';
        toggleBtn.classList.remove('active');
    }
}
function toggleSubcategoryItems(subcategoryId) {
    const itemsList = document.getElementById('subcat-items-' + subcategoryId);
    const toggleBtn = document.getElementById('subcat-toggle-' + subcategoryId);
    document.querySelectorAll('.mobile-subcat-items').forEach(list => {
        if (list.id !== 'subcat-items-' + subcategoryId) { list.style.display = 'none'; }
    });
    document.querySelectorAll('.mobile-subcategory-toggle').forEach(btn => {
        if (btn.id !== 'subcat-toggle-' + subcategoryId) { btn.classList.remove('active'); }
    });
    if (itemsList.style.display === 'none' || itemsList.style.display === '') {
        itemsList.style.display = 'block';
        toggleBtn.classList.add('active');
    } else {
        itemsList.style.display = 'none';
        toggleBtn.classList.remove('active');
    }
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeMobileCategoryMenu(); }
});
</script>