import esbuild from 'esbuild'
import { execSync, spawn } from 'child_process'

const isDev = process.argv.includes('--dev')

async function compile(options) {
    const context = await esbuild.context(options)

    if (isDev) {
        await context.watch()
    } else {
        await context.rebuild()
        await context.dispose()
    }
}

const defaultOptions = {
    define: {
        'process.env.NODE_ENV': isDev ? `'development'` : `'production'`,
    },
    bundle: true,
    mainFields: ['module', 'main'],
    platform: 'neutral',
    sourcemap: isDev ? 'inline' : false,
    sourcesContent: isDev,
    treeShaking: true,
    target: ['es2020'],
    minify: !isDev,
    plugins: [{
        name: 'watchPlugin',
        setup: function (build) {
            build.onStart(() => {
                console.log(`Build started at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`)
            })

            build.onEnd((result) => {
                if (result.errors.length > 0) {
                    console.log(`Build failed at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`, result.errors)
                } else {
                    console.log(`Build finished at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`)
                }
            })
        }
    }],
}

compile({
    ...defaultOptions,
    entryPoints: ['./resources/js/index.ts'],
    outfile: './resources/dist/nuwa.js',
}).then(() => {
    console.log(`Build completed for nuwa.js`)
})

compile({
    ...defaultOptions,
    entryPoints: ['./resources/js/editor/index.ts'],
    outfile: './resources/dist/nuwa-editor.js',
}).then(() => {
    console.log(`Build completed for nuwa-editor.js`)
})

// CSS build via Tailwind CSS v4 CLI
const cssInput = './resources/css/index.css'
const cssOutput = './resources/dist/nuwa.css'

if (isDev) {
    const tailwind = spawn('npx', ['tailwindcss', '-i', cssInput, '-o', cssOutput, '--watch'], {
        stdio: 'inherit',
        shell: true,
    })

    tailwind.on('error', (err) => {
        console.error(`Tailwind CSS watch error: ${err.message}`)
    })
} else {
    try {
        execSync(`./node_modules/.bin/tailwindcss -i ${cssInput} -o ${cssOutput} --minify`, { stdio: 'inherit' })
        console.log(`Build completed for nuwa.css`)
    } catch (err) {
        console.error(`Tailwind CSS build failed`)
        process.exit(1)
    }
}
