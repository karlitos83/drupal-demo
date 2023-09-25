# Drupal Project Showcase

This project serves as a showcase of various tasks that can be accomplished with Drupal. Below are some of the key exercises and features that demonstrate some of my capabilities and skills with Drupal:

## Migration Exercise
- CSV file migration to populate the 'pokemon_types' and 'pokemon_colors' vocabularies using the custom module 'sample_migrate.'

## Drush Command Exercise
- Utilization of a Drush command provided by the custom module 'sample_content' to fetch data from the [PokeAPI](https://pokeapi.co/) and subsequently load first-generation Pokémon into a custom content type called "Pokemon."

## Event Subscriber Exercise
- Creation of a user triggers an event that adds a default value to the 'field_pokeballs' field.
- The user's first login generates a node of the 'pokedollars_balance' content type with an initial amount and a description of the concept.

## Site Building Exercise
- Creation of Views:
  - Revision view for the 'balance' content type, which is related to the user through an entity reference field.
  - Pokedex view: User profiles have an entity reference field to the 'pokemon' content type, listing discovered/added Pokémon.
  - Both views use contextual filters to display content specific to the user.

## Custom Modules
- Three custom modules were built:
  - 'sample_content' for the Drush command.
  - 'sample_migrate' for vocabulary migration.
  - 'sample_trainer' for user-related Pokémon trainer information.

## System Requirements
- Docker and Lando must be installed to run this project.

## Installation Instructions
1. Clone the project repository.
2. Navigate to the 'drupal-demo' directory.
3. Initialize Lando using `lando start`.
4. Execute `lando override` to deploy the project, which will begin capturing Pokémon data in its respective content type.
5. Access a URL provided by Lando.
6. Log in as the root user with the following credentials: Username - root, Password - toor.
7. As an anonymous user, you can create an account and log in.
8. All content will be displayed on the user page.
9. When editing, users can add Pokémon to their Pokedex as desired.

Feel free to explore this Drupal project's capabilities and functionalities.

## TODO List

- [ ] Implement random Pokémon summoning through an API request to an endpoint created with 'webform_rest' and record the summoning in a webform.

- [ ] Enable the ability to capture a Pokémon if the user has available Pokéballs, based on the Pokémon's capture rate.

- [ ] Update the webform submission via an API request to the webform endpoint with the results of the Pokémon capture. The controller should also update the user's profile with the captured Pokémon.

- [ ] Build a call to action that triggers JavaScript to display the randomly summoned Pokémon and a button to attempt capturing the Pokémon while it's present.

