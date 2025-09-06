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
                    blue: "#073159", // light shade
                    yellow: "#FFA600", // main shade
                    dark: "#272B34", // dark shade
                },
            },
            fontFamily: {
                times: ['"Times New Roman"', "serif"],
            },
        },
    },
    plugins: [],
};
