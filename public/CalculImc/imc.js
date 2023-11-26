const selectElement = (selector, parent = document) => {
    const el = parent.querySelector(selector);
    return el || null;
};

  // Grab elements
  const themeToggleBtn = selectElement("#theme-toggle-btn");
  const menuToggleBtn = selectElement("#menu-toggle-btn");
  const searchFormIconBtn = selectElement("#search-form-icon-btn");
  const searchformCloseBtn = selectElement("#search-form-close-btn");
  
  /**
   * Menu toggle
   */
  const toggleMenu = () => {
    const menuElement = selectElement(".menu");
    menuElement.classList.toggle("active");
    menuToggleBtn.classList.toggle("active");
  };
  
  /**
   * Light and Dark mode switch
   */
  const toggleTheme = () => {
    const body = selectElement("body");
    const moonIcon = selectElement(".moon-icon");
    const sunIcon = selectElement(".sun-icon");

    body.classList.toggle("dark-theme");
    themeToggleBtn.classList.toggle("active");

    // Toggle icon visibility
    moonIcon.style.display = body.classList.contains("dark-theme") ? "block" : "none";
    sunIcon.style.display = body.classList.contains("dark-theme") ? "none" : "block";
};

  
  /**
   * Search toggle
   */
  const toggleSearchForm = () => {
    const searchFormContainer = selectElement("#search-form-container");
    searchFormContainer.classList.toggle("active");
  };
  
  /* engine */
  menuToggleBtn.addEventListener("click", toggleMenu);
  themeToggleBtn.addEventListener("click", toggleTheme);
  searchFormIconBtn.addEventListener("click", toggleSearchForm);
  searchformCloseBtn.addEventListener("click", toggleSearchForm);
  