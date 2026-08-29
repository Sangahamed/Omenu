<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-white text-ink">

    
    <div class="relative h-[350px] md:h-[450px] overflow-hidden bg-brand-black">
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($restaurant->cover_image): ?>
            <img src="<?php echo e(asset('storage/' . $restaurant->cover_image)); ?>" alt="<?php echo e($restaurant->name); ?>" class="w-full h-full object-cover opacity-80">
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center">
                <i class="ri-restaurant-2-line text-8xl text-white/10"></i>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="absolute inset-0 bg-gradient-to-t from-brand-black via-brand-black/70 to-transparent"></div>

        
        <div class="absolute bottom-0 inset-x-0 pb-10">
            <div class="container mx-auto px-4 md:px-8">
                <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-2 text-xs uppercase tracking-widest font-semibold text-white hover:text-brand-red transition-colors mb-4 group">
                    <i class="ri-arrow-left-line transition-transform group-hover:-translate-x-1"></i> Explorer les adresses
                </a>

                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <span class="px-3 py-1 bg-brand-red text-white text-xs uppercase font-bold tracking-wider rounded-pill">
                            <?php echo e($restaurant->cuisine_type); ?>

                        </span>
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-display font-semibold text-white tracking-tight mt-3 mb-2">
                            <?php echo e($restaurant->name); ?>

                        </h1>
                        <p class="text-[#C9C9C6] text-sm md:text-base max-w-2xl font-display italic mb-4">
                            "<?php echo e($restaurant->description ?? 'Une expérience gastronomique d\'exception vous attend.'); ?>"
                        </p>

                        <div class="flex flex-wrap items-center gap-y-2 gap-x-6 text-xs text-[#C9C9C6] font-medium">
                            <span class="flex items-center gap-1.5">
                                <i class="ri-map-pin-line text-brand-red text-sm"></i>
                                <?php echo e($restaurant->address); ?>, <?php echo e($restaurant->city); ?>

                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="ri-price-tag-3-line text-brand-red text-sm"></i>
                                Gamme : <span class="font-bold text-white"><?php echo e($restaurant->price_range); ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="container mx-auto px-4 md:px-8 py-12">
        <div class="grid lg:grid-cols-3 gap-12">

            
            <div class="lg:col-span-2 space-y-10">
                <div class="categorie-titre">
                    <h2 class="text-2xl font-display font-semibold text-brand-black tracking-tight mb-2 flex items-center gap-3">
                        <span class="w-8 h-px bg-brand-red"></span> Notre Carte Gastronomique
                    </h2>
                    <p class="text-xs text-ink-soft uppercase tracking-widest font-semibold pl-11">Plats préparés à la commande par notre Chef</p>
                </div>

                
                <div class="grid sm:grid-cols-2 gap-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $restaurant->menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="plat-card bg-white border border-border hover:border-brand-black rounded-md p-4 flex flex-col justify-between transition-all duration-300 group">
                            <div class="flex gap-4">
                                
                                <div class="w-20 h-20 bg-[#EFEFEC] rounded-sm overflow-hidden flex-shrink-0 border border-border flex items-center justify-center">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($menu->image): ?>
                                        <img src="<?php echo e(asset('storage/' . $menu->image)); ?>" alt="<?php echo e($menu->name); ?>" class="w-full h-full object-cover grayscale-[0.15] group-hover:scale-105 transition-transform duration-500">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-ink-soft">
                                            <i class="ri-restaurant-line text-2xl"></i>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div class="min-w-0">
                                    <span class="text-[10px] uppercase font-bold tracking-wider text-brand-red">
                                        <?php echo e($menu->category ?? 'Plat signature'); ?>

                                    </span>
                                    <h3 class="font-display font-semibold text-ink text-base mt-0.5 group-hover:text-brand-red transition-colors truncate">
                                        <?php echo e($menu->name); ?>

                                    </h3>
                                    <p class="text-xs text-ink-soft mt-1 line-clamp-2 leading-relaxed">
                                        <?php echo e($menu->description ?? 'Préparation raffinée élaborée à partir d\'ingrédients de premier choix.'); ?>

                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-4 pt-3 border-t border-border">
                                <span class="text-brand-red font-display font-bold text-sm">
                                    <?php echo e(number_format($menu->price)); ?> FCFA
                                </span>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($menu->is_available ?? true): ?>
                                    <button
                                        wire:click="$dispatch('addToCart', { menuId: <?php echo e($menu->id); ?> })"
                                        class="btn-add px-4 py-1.5 rounded-sm bg-white hover:bg-brand-black text-brand-black hover:text-white border border-brand-black font-semibold text-xs transition-all duration-300">
                                        Ajouter au panier
                                    </button>
                                <?php else: ?>
                                    <span class="text-[10px] uppercase tracking-wider text-brand-red font-semibold">Indisponible</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-span-full py-16 text-center border border-dashed border-border rounded-md">
                            <i class="ri-compass-3-line text-5xl text-ink-soft block mb-4"></i>
                            <h3 class="font-display font-semibold text-ink text-lg">La carte est en cours de création</h3>
                            <p class="text-xs text-ink-soft mt-1">Revenez très bientôt pour découvrir nos créations culinaires.</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="lg:col-span-1">
                <div class="bg-white border border-border rounded-md p-6 sticky top-28 space-y-6">
                    <div>
                        <h3 class="text-lg font-display font-semibold text-ink flex items-center gap-2">
                            <i class="ri-sparkling-2-line text-brand-red"></i> Suggestions du Chef
                        </h3>
                        <p class="text-[10px] text-ink-soft uppercase tracking-widest font-semibold mt-1">Sélections recommandées par l'établissement</p>
                    </div>

                    
                    <div class="space-y-4">

                        
                        <div class="flex items-center gap-3 p-3 bg-white rounded-sm border border-border">
                            <div class="w-12 h-12 bg-[#EFEFEC] rounded-sm flex-shrink-0 flex items-center justify-center">
                                <i class="ri-goblet-line text-xl text-brand-red"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-display font-bold text-ink truncate">Sélection de Vin du Sommelier</h4>
                                <p class="text-[10px] text-ink-soft line-clamp-1">L'accord parfait pour sublimer votre dîner</p>
                                <span class="text-[11px] font-mono font-bold text-brand-red mt-0.5 block">À la carte</span>
                            </div>
                        </div>

                        
                        <div class="flex items-center gap-3 p-3 bg-white rounded-sm border border-border">
                            <div class="w-12 h-12 bg-[#EFEFEC] rounded-sm flex-shrink-0 flex items-center justify-center">
                                <i class="ri-cake-3-line text-xl text-brand-red"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-display font-bold text-ink truncate">Le Secret Chocolaté de Saison</h4>
                                <p class="text-[10px] text-ink-soft line-clamp-1">Mousse au chocolat grand cru et éclats de fèves</p>
                                <span class="text-[11px] font-mono font-bold text-brand-red mt-0.5 block">Dessert du moment</span>
                            </div>
                        </div>

                        
                        <div class="flex items-center gap-3 p-3 bg-white rounded-sm border border-border">
                            <div class="w-12 h-12 bg-[#EFEFEC] rounded-sm flex-shrink-0 flex items-center justify-center">
                                <i class="ri-leaf-line text-xl text-brand-red"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-display font-bold text-ink truncate">Option "Chef d'Œuvre" Bio</h4>
                                <p class="text-[10px] text-ink-soft line-clamp-1">Certains de nos plats sont déclinables en version végétarienne</p>
                                <span class="text-[11px] font-mono font-bold text-brand-red mt-0.5 block">Sur demande</span>
                            </div>
                        </div>

                    </div>

                    
                    <div class="p-4 bg-brand-red-soft border border-brand-red/20 rounded-sm flex gap-3 text-xs text-ink-soft">
                        <i class="ri-shield-user-line text-brand-red text-lg flex-shrink-0"></i>
                        <div>
                            <h4 class="font-display font-bold text-ink mb-0.5">Service de livraison</h4>
                            <p class="leading-relaxed text-[11px]">Notre propre équipe de coursiers assure un acheminement sécurisé de vos plats.</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('components.front.layouts.front', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\omenu-charte\resources\views/restaurant/show.blade.php ENDPATH**/ ?>