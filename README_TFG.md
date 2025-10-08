<p align="center">
    <em>Refer to README.md in this same directory for further and more detailed information</em>
</p>  

<p align="center">
  <em>📚 Need more details?</em><br>
  🔗 Visit the <a href="./README.md"><b>Extended Documentation</b></a> for advanced configuration, usage, and troubleshooting.
</p>

## Instrucciones de interacción con la aplicación

<h1 align="center">TFG_ENVIRONMENT</h1>

<h1 align="center">Aplicativo de planificación y organización integral adaptado a entornos empresariales y personales. </h1>
<h3 align="center">Autor: Alba Mayol Lozano | alba.mayol@students.salle.url.edu</h3>

<p align="center">
    <em>Escuela Técnica Superior de Ingeniería La Salle</em>
    <em>Trabajo de Fin de Grado </em>
    <em>Grado en Ingeniería Informática</em>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/last%20commit-september-555555?style=flat-square" alt="Last Commit">
  <img src="https://img.shields.io/badge/php-95.2%25-blue?style=flat-square" alt="PHP">
  <img src="https://img.shields.io/badge/languages-5-brightgreen?style=flat-square" alt="Languages">
</p>

---

### 🛠️ Built with the tools and technologies:

<p align="center">
  <img src="https://img.shields.io/badge/HTML5-E34F26?style=flat&logo=html5&logoColor=white" />
  <img src="https://img.shields.io/badge/CSS3-1572B6?style=flat&logo=css3&logoColor=white" />
  <img src="https://img.shields.io/badge/JSON-000000?style=flat&logo=json&logoColor=white" />
  <img src="https://img.shields.io/badge/Markdown-000000?style=flat&logo=markdown&logoColor=white" />
  <img src="https://img.shields.io/badge/Composer-885630?style=flat&logo=composer&logoColor=white" />
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=flat&logo=javascript&logoColor=black" />
  <img src="https://img.shields.io/badge/Nginx-009639?style=flat&logo=nginx&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white" />
  <img src="https://img.shields.io/badge/Docker-2496ED?style=flat&logo=docker&logoColor=white" />
  <img src="https://img.shields.io/badge/phpMyAdmin-6C78AF?style=flat&logo=phpmyadmin&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/YAML-CB171E?style=flat&logo=yaml&logoColor=white" />
</p>

---

## 🧩 Overview

**TFG_environment** is a comprehensive, Docker-based development setup designed for **PHP applications**.  
It ensures consistent, containerized environments for local development, testing, and deployment — integrating essential dependencies, extensions, and tools like **WKHTMLTOPDF** and **XDebug**.

---

### 💡 Why TFG_environment?

This project simplifies the complexities of setting up a PHP environment by providing a ready-to-use, orchestrated architecture.  
The core features include:

- 🐳 **Containerized PHP Runtime:** Tailored for PHP 8.4.3 with necessary libraries and extensions.  
- 🌐 **Multi-Service Orchestration:** Seamlessly manages web server, database, and application services via Docker Compose.  
- 🧪 **Development & Testing Support:** Built-in configurations for unit testing, CLI commands, and environment customization.  
- 🔒 **Security & Performance:** Implements access restrictions, PHP preloading, and caching for optimized workflows.  
- 🚀 **Developer-Friendly:** Facilitates efficient local development, debugging, and deployment with minimal setup.  

---

<p align="center">
  <em>📦 Designed for developers who value speed, consistency, and simplicity.</em>
</p>

---

## 🚀 Getting Started

### 📋 Prerequisites

This project requires the following dependencies:

- **Programming Language:** PHP  
- **Package Manager:** Composer  
- **Container Runtime:** Docker  

---

### ⚙️ Installation

Build **TFG_environment** from the source and install dependencies:

1. **Clone the repository:**
```bash
git clone https://github.com/albamayol/TFG_environment
```
2. **Navigate to the project directory:**
```bash
cd TFG_environment
```
3. **Install the dependencies**
    - Using Docker
```bash
        docker build -t albamayol/TFG_environment .
```
    - Using Composer
```bash
        composer install
```  
