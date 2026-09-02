module.exports = {
  content: [
    "./addon/Views/layout.php",
    "./addon/Views/dashboard/*.php",
    "./addon/Views/password/forgot.php",
    "./addon/Views/password/reset.php",
    "./addon/Views/admin/**/*.php",
    "./addon/Views/error/*.php",
    "./addon/Views/profile.php",
    "./addon/Views/pendaftaran/*.php",
    "./addon/Views/(auth)/login.php",
    "./addon/Views/(auth)/register.php",
    "./addon/Views/(auth)/verify-otp.php",
    "./addon/Views/(auth)/otp-sent.php",
    "./addon/Views/(auth)/layout.php",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ["Inter", "system-ui", "sans-serif"],
        display: ["Poppins", "sans-serif"],
      },
    },
  },
  plugins: [],
};
