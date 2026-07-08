module.exports = {
  content: [
    "./addon/Views/layout.php",
    "./addon/Views/dashboard/index.php",
    "./addon/Views/password/forgot.php",
    "./addon/Views/password/reset.php",
    "./addon/Views/(auth)/login.php",
    "./addon/Views/(auth)/register.php",
    "./addon/Views/(auth)/verify-otp.php",
    "./addon/Views/(auth)/otp-sent.php",
    "./addon/Views/(auth)/layout.php"
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        display: ['Poppins', 'sans-serif'],
      }
    },
  },
  plugins: [],
}
