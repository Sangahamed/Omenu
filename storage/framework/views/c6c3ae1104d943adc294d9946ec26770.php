<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'OMenu')); ?></title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="font-sans text-ink antialiased">
        <div class="min-h-screen flex bg-bg">

            
            <div class="hidden lg:flex lg:w-5/12 bg-brand-black text-white flex-col justify-between p-12 relative overflow-hidden">
                <a href="/" class="relative z-10">
                    <span class="font-display text-2xl font-semibold tracking-tight text-white">
                        O<span class="text-brand-red">Menu</span>
                    </span>
                </a>

                <div class="relative z-10">
                    <p class="font-display italic text-2xl leading-snug text-white/90 max-w-sm">
                        "Le menu digital et la réservation, pensés pour les restaurants de Côte d'Ivoire."
                    </p>
                    <div class="w-10 h-px bg-brand-red mt-6"></div>
                    <p class="text-xs uppercase tracking-widest text-white/50 mt-4">OMenu.ci</p>
                </div>

                
                <div class="absolute -right-24 -bottom-24 w-96 h-96 rounded-full border border-white/10 pointer-events-none"></div>
                <div class="absolute -right-10 -bottom-40 w-72 h-72 rounded-full border border-brand-red/20 pointer-events-none"></div>
            </div>

            
            <div class="flex-1 flex flex-col justify-center items-center px-6 py-12">
                <div class="w-full sm:max-w-sm">
                    <a href="/" class="lg:hidden flex justify-center mb-8">
                        <span class="font-display text-2xl font-semibold tracking-tight text-brand-black">
                            O<span class="text-brand-red">Menu</span>
                        </span>
                    </a>

                    <div class="auth-box bg-white border border-border lg:border-none rounded-md lg:rounded-none p-6 lg:p-0">
                        <?php echo e($slot); ?>

                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<?php /**PATH C:\xampp\htdocs\omenu-charte\resources\views/layouts/guest.blade.php ENDPATH**/ ?>