# Message board

You can add messages here.

# Prerequisites

```PHP >=7.1```

# Installation

- Create mysql database and import `DB.sql` dump.

- Open `App/Config.php` and fill config file with Database info

- Simply run:
```bash 
composer install
```
It will generate all required psr-4 autoload files

# Usage

- Load page.
- Fill out message form.
- Push `Skelbti` button to insert message. If Javascript is enabled it uses ajax call to backend, otherwise it uses form post action

