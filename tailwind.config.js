module.exports = {
  content: [],
  theme: {
    colors: {
      'blue': '#1fb6ff',
    },

    fontFamily: {
      'sans': ['Graphik', 'sans-serif'],
      'body': ['poppins'],


    },

    extend: {
      borderRadius: {
        '4xl': '2rem',
      }
    },

    
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/aspect-ratio'),
    require('@tailwindcss/typography'),
    require('tailwindcss-children'),
    require('flowbite/plugin')
  ],
}
