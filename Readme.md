# Instalacja modułu BLPaczka dla Magento 2.4.3-2.4.7
Ten dokument opisuje kroki instalacji modułu BLPaczka w sklepie opartym na Magento 2.4.3-2.4.7

# Wymagania wstępne
- Magento 2.4.3-2.4.7
- PHP 7.4-8.3
- Pliki modułu BLPaczka

# Instalacja
1. Utwórz katalog `app/code/BLPaczka/MagentoIntegration` w katalogu głównym Magento.
2. Skopiuj tam wszystkie pliki modułu.
3. Uruchom wdrożenie Magento (deploy): `bin/magento setup:upgrade && bin/magento setup:di:compile && bin/magento setup:static-content:deploy && bin/magento cache:flush`.
4. Sprawdź, czy moduł jest zainstalowany, uruchom: `bin/magento module:status BLPaczka_MagentoIntegration`;
5. Jeśli moduł jest wyłączony, uruchom: `bin/magento module:enable BLPaczka_MagentoIntegration`.

# Konfiguracja
Konfiguracja znajduje się w panelu administracyjnym w lokalizacjach:

1. `Stores -> Configuration -> Sales -> Delivery Methods -> BLPaczka`.
2. `Stores -> Configuration -> Sales -> BLPaczka`.

# Pomoc i wsparcie

Jeśli napotkasz problemy podczas instalacji lub konfiguracji modułu, skontaktuj się z naszym działem wsparcia technicznego (https://blpaczka.com/kontakt).

---
# Installing the BLPaczka module for Magento 2.4.3-2.4.7
This document describes the steps for installing the BLPaczka module in a store based on Magento 2.4.3-2.4.7

# Prerequisites
- Magento 2.4.3-2.4.7
- PHP 7.4-8.3
- The BLPaczka module files

# Installation
1. Create the `app/code/BLPaczka/MagentoIntegration` directory in the Magento root directory.
2. Copy all module files there.
3. Run the Magento deployment: `bin/magento setup:upgrade && bin/magento setup:di:compile && bin/magento setup:static-content:deploy && bin/magento cache:flush`.
4. Check if the module is installed, run: `bin/magento module:status BLPaczka_MagentoIntegration`;
5. If the module is disabled, run: `bin/magento module:enable BLPaczka_MagentoIntegration`;

# Configuration
The configuration is located in the admin panel in the following locations:

1. `Stores -> Configuration -> Sales -> Delivery Methods -> BLPaczka`.
2. `Stores -> Configuration -> Sales -> BLPaczka`.

# Help and support

If you encounter any problems during the installation or configuration of the module, please contact our technical support department (https://blpaczka.com/kontakt).
