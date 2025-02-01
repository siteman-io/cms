import preset from  './vendor/filament/filament/tailwind.config.preset'

module.exports = {
    presets: [preset],
    content: [
        './src/Filament/**/*.php',
        './resources/views/**/*.blade.php',
    ],
}
