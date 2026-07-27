# install deps, seed and run project with sail
init:
    composer install
    ./vendor/bin/sail up -d
    ./vendor/bin/sail npm install
    ./vendor/bin/sail artisan migrate
    ./vendor/bin/sail artisan db:seed
    just run

# run with sail, vite, the queue worker and the websocket server
run:
    ./vendor/bin/sail up -d
    tmux new-session -d -s "casa"
    tmux send-keys -t "casa" "just vite" ENTER
    tmux new-window -t "casa" -n "queue"
    tmux send-keys -t "casa:queue" "just queue" ENTER
    tmux new-window -t "casa" -n "reverb"
    tmux send-keys -t "casa:reverb" "just reverb" ENTER

# Run front with vite
vite:
  ./vendor/bin/sail npm run dev

# Run the queue worker, the bookmark import runs on it
queue:
    ./vendor/bin/sail artisan queue:work --tries=3

# Run the websocket server, it carries the bookmark import progress
reverb:
    ./vendor/bin/sail artisan reverb:start --debug

# Lint front code with biome
biome:
    ./vendor/bin/sail npm run lint

# Lint front and back code
lint:
    just biome
    just pint_fix

# Test and fix files with Pint
pint_fix file="":
    ./vendor/bin/pint {{file}}

# Install all or one deps with npm
install dep="":
  ./vendor/bin/sail npm install {{dep}}

# Run a command with artisan
artisan *cmd:
  ./vendor/bin/sail artisan {{cmd}}

# Run the scheduled commands, newsletter catalog included
schedule:
    ./vendor/bin/sail artisan schedule:work

# Fill the newsletter catalog from the awesome-newsletters repository
sync_newsletters:
    ./vendor/bin/sail artisan newsletters:sync-catalog
    ./vendor/bin/sail artisan newsletters:sync-logos --limit=300

# Open admin
go_adminer:
    open "http://localhost:8080/?server=pgsql&username=sail&db=laravel"

# Generate a key with artisan
generate:
    ./vendor/bin/sail artisan key:generate

# Seed DB, the user ends up with everything set up
seed:
    ./vendor/bin/sail artisan db:seed

# Seed a user who has never been through the onboarding, their tags, links and
# newsletters are emptied first. Accounts and the newsletter catalog are kept.
seed_fresh:
    ./vendor/bin/sail artisan db:seed --class=FreshUserSeeder

# Seed a user who is done with the onboarding, with tags, links and newsletters
seed_onboarded:
    ./vendor/bin/sail artisan db:seed --class=OnboardedUserSeeder

# Build front
build:
    ./vendor/bin/sail npm run build

# Run tests
tests:
    ./vendor/bin/pest
