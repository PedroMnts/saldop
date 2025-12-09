#!/bin/bash

# Setup script for Saldop project

echo "Setting up Saldop project..."

# Copy .env files from examples if they don't exist
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✓ Created .env from .env.example"
fi

if [ ! -f .env.test ]; then
    cp .env.test.example .env.test
    echo "✓ Created .env.test from .env.test.example"
fi

if [ ! -f .env.dev ]; then
    cp .env.example .env.dev
    echo "✓ Created .env.dev from .env.example"
fi

if [ ! -f .env.docker ]; then
    cp .env.example .env.docker
    echo "✓ Created .env.docker from .env.example"
fi

echo ""
echo "Setup complete!"
echo "Remember to update the .env files with your local configuration."
