<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 md:px-8 py-12">

    <div class="mb-10">
        <h1 class="text-3xl md:text-4xl font-display font-semibold text-brand-black tracking-tight mb-2">
            Nos adresses partenaires
        </h1>
        <p class="text-ink-soft text-sm"><?php echo e($restaurants->total()); ?> établissement(s) disponible(s)</p>
    </div>

    
    <form method="GET" action="<?php echo e(route('restaurants.index')); ?>" class="flex flex-wrap gap-3 mb-10">
        <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Nom, ville, type de cuisine..."
               class="flex-1 min-w-[220px] border-border focus:border-brand-red focus:ring-brand-red rounded-sm px-4 py-2 text-sm">
        <button type="submit" class="bg-brand-black hover:bg-brand-black-2 text-white text-sm font-semibold px-6 py-2 rounded-sm transition">
            Rechercher
        </button>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->hasAny(['q', 'city', 'cuisine_type'])): ?>
            <a href="<?php echo e(route('restaurants.index')); ?>" class="text-sm text-ink-soft hover:text-brand-black self-center underline">
                Réinitialiser
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </form>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $restaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e(route('restaurants.show', $restaurant->slug)); ?>"
               class="restaurant-card group bg-white border border-border hover:border-brand-black rounded-md overflow-hidden transition-all duration-300">
                <div class="h-40 bg-[#EFEFEC] overflow-hidden">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($restaurant->cover_image): ?>
                        <img src="<?php echo e(asset('storage/' . $restaurant->cover_image)); ?>" alt="<?php echo e($restaurant->name); ?>"
                             class="w-full h-full object-cover grayscale-[0.15] group-hover:scale-105 transition-transform duration-500">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-ink-soft">
                            <i class="ri-restaurant-2-line text-4xl"></i>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="p-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($restaurant->cuisine_type): ?>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-brand-red">
                            <?php echo e($restaurant->cuisine_type); ?>

                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <h3 class="font-display font-semibold text-ink text-lg mt-0.5 group-hover:text-brand-red transition-colors truncate">
                        <?php echo e($restaurant->name); ?>

                    </h3>

                    <p class="text-xs text-ink-soft mt-1 flex items-center gap-1.5">
                        <i class="ri-map-pin-line text-brand-red"></i>
                        <?php echo e($restaurant->city); ?>

                    </p>

                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-border">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($restaurant->average_rating > 0): ?>
                            <span class="text-xs text-ink-soft flex items-center gap-1">
                                <i class="ri-star-fill text-brand-red"></i>
                                <?php echo e(number_format($restaurant->average_rating, 1)); ?>

                            </span>
                        <?php else: ?>
                            <span class="text-xs text-ink-soft italic">Nouveau</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($restaurant->price_range): ?>
                            <span class="text-xs font-mono text-ink-soft"><?php echo e($restaurant->price_range); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full text-center py-20">
                <i class="ri-search-eye-line text-5xl text-ink-soft/40 block mb-4"></i>
                <h3 class="text-lg font-display font-medium text-ink-soft">Aucun restaurant trouvé</h3>
                <p class="text-xs text-ink-soft mt-1">Essayez une autre recherche.</p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="mt-10">
        <?php echo e($restaurants->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('components.front.layouts.front', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\omenu-charte\resources\views/restaurant/index.blade.php ENDPATH**/ ?>