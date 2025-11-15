/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    green:"#248639", // dark shade
                    gray:"#D9D9D9", // dark shade
                    highlight: "#42A457", // selected shade
                },
            },
            fontFamily: {
                poppins: ['"Poppins"', "sans-serif"],
            },
        },
    },
    plugins: [],
};
