<?php

/**
 * read a line of input from the user with an optional prompt
 * returns the line as a string or, if the user hits ^D, boolean false
 */
function get_user_input($prompt = null)
{
   $line = readline($prompt);
   return $line ? $line : null; // no boolean return 
}

/**
 * Prompt user to choose one value from $options array
 *
 * @param  String  $prompt  Text of prompt to display
 * @param  Array   $options Array of valid options. Must be chars.
 * @return String  The valid char selected by the user
 */
function prompt_choice($prompt = "Choose One", $options = ['yes', 'no'])
{
   // Force options values to single chars
   $options = array_unique(array_map(fn($o) => (string)$o[0], $options));

   // Prompt user to choose an option until they select a valid value
   while (true) {
      // Create a prompt that lists the options
      $showPrompt = "$prompt [" . implode(', ', $options) . "]: ";

      // Read input from the user
      $keystroke = strtolower(trim(readline($showPrompt)));

      // Return selected value if valid
      if (in_array($keystroke, $options)) {
         return $keystroke;
      }

      // No valid choice. Show menu again
      echo "Invalid choice. Please select a valid option." . PHP_EOL;
   }
}

/**
 * entry point
 */
echo "Welcome to the Number Guessing Game!" . PHP_EOL;
echo "I'm thinking a number between 1 and 100." . PHP_EOL;
echo "You have to guess it!" . PHP_EOL;
echo PHP_EOL;

$keepPlaying = true;
$chances = 0;

while ($keepPlaying) {
   echo PHP_EOL . "Please select the difficulty level:" . PHP_EOL;
   echo "1. Easy (10 chances)" . PHP_EOL;
   echo "2. Medium (5 chances)" . PHP_EOL;
   echo "3. Hard (3 chances)" . PHP_EOL;
   echo PHP_EOL;

   $difficulty = (int) get_user_input("Enter your choice: ");
   echo PHP_EOL;

   while (true) {
      if ($difficulty == 1) {
         $chances = 10;
         echo "Great! You have selected the Medium difficulty level." . PHP_EOL;
         break;
      } elseif ($difficulty == 2) {
         $chances = 5;
         echo "Great! You have selected the Medium difficulty level." . PHP_EOL;
         break;
      } elseif ($difficulty == 3) {
         $chances = 3;
         echo "Great! You have selected the Hard difficulty level." . PHP_EOL;
         break;
      } else {
         echo "Invalid choice. Please select a valid difficulty level." . PHP_EOL;
         $difficulty = get_user_input("Enter your choice: ");
      }
   }

   echo "Let's start the game!" . PHP_EOL;

   $random_number = rand(1, 100);
   $guesses = [];

   while (!in_array($random_number, $guesses) && $chances > 0) {
      echo PHP_EOL;
      $guess = (int) get_user_input("Enter your guess: ");
      $guesses[] = $guess;
      $chances--;

      if ($guess < $random_number) {
         echo "Incorrect! The number is greater than $guess." . PHP_EOL;
      } elseif ($guess > $random_number) {
         echo "Incorrect! The number is less than $guess." . PHP_EOL;
      } else {
         echo "Congratulations! You guessed the correct number in " . (count($guesses)) . " attempts." . PHP_EOL;
         break;
      }
   }

   if ($chances == 0) {
      echo PHP_EOL;
      echo "Sorry! You've used all your chances. The correct number was $random_number." . PHP_EOL;
   }

   echo PHP_EOL;
   echo "Your guesses were: " . implode(", ", $guesses) . PHP_EOL;
   $playAgain = prompt_choice("Do you want to play again? ");
   if (strtolower($playAgain) == 'n') {
      echo "Exiting the game." . PHP_EOL;
      $keepPlaying = false;
      break;
   } elseif (strtolower($playAgain) != 'y') {
      echo "Invalid input. Please enter 'yes' or 'no'." . PHP_EOL;
      continue;
   }
}
