<?php

$highScores = [
   'easy' => ['attempts' => PHP_INT_MAX, 'time' => PHP_INT_MAX],
   'medium' => ['attempts' => PHP_INT_MAX, 'time' => PHP_INT_MAX],
   'hard' => ['attempts' => PHP_INT_MAX, 'time' => PHP_INT_MAX],
];

/**
 * Add color to text output
 */
function color_text($text, $color)
{
   $colors = [
      'red' => "\033[31m",
      'green' => "\033[32m",
      'yellow' => "\033[33m",
      'blue' => "\033[34m",
      'cyan' => "\033[36m",
      'reset' => "\033[0m"
   ];
   return $colors[$color] . $text . $colors['reset'];
}

/**
 * Read a line of input from the user with an optional prompt
 */
function get_user_input($prompt = null)
{
   $line = readline($prompt);
   return $line ? $line : null;
}

/**
 * Prompt user to choose one value from $options array
 */
function prompt_choice($prompt = "Choose One", $options = ['yes', 'no'])
{
   $options = array_unique(array_map(fn($o) => (string)$o[0], $options));

   while (true) {
      $showPrompt = "$prompt [" . implode(', ', $options) . "]: ";
      $keystroke = strtolower(trim(readline($showPrompt)));

      if (in_array($keystroke, $options)) {
         return $keystroke;
      }

      echo color_text("Invalid choice. Please select a valid option." . PHP_EOL, 'red');
   }
}

/**
 * Entry point
 */
echo color_text("========================================", 'blue') . PHP_EOL;
echo color_text("  Welcome to the Number Guessing Game!  ", 'yellow') . PHP_EOL;
echo color_text("========================================", 'blue') . PHP_EOL;
echo "I'm thinking of a number between 1 and 100." . PHP_EOL;
echo "You have to guess it!" . PHP_EOL;

$keepPlaying = true;

while ($keepPlaying) {
   echo PHP_EOL . "Please select the difficulty level:" . PHP_EOL;
   echo color_text("1. Easy (10 chances)", 'green') . PHP_EOL;
   echo color_text("2. Medium (5 chances)", 'yellow') . PHP_EOL;
   echo color_text("3. Hard (3 chances)", 'red') . PHP_EOL;

   $difficulty = (int) get_user_input("Enter your choice: ");
   $chances = 0;
   $difficultyKey = '';

   while (true) {
      if ($difficulty == 1) {
         $chances = 10;
         $difficultyKey = 'easy';
         echo color_text("Great! You have selected the Easy difficulty level." . PHP_EOL, 'green');
         break;
      } elseif ($difficulty == 2) {
         $chances = 5;
         $difficultyKey = 'medium';
         echo color_text("Great! You have selected the Medium difficulty level." . PHP_EOL, 'yellow');
         break;
      } elseif ($difficulty == 3) {
         $chances = 3;
         $difficultyKey = 'hard';
         echo color_text("Great! You have selected the Hard difficulty level." . PHP_EOL, 'red');
         break;
      } else {
         echo color_text("Invalid choice. Please select a valid difficulty level." . PHP_EOL, 'red');
         $difficulty = (int) get_user_input("Enter your choice: ");
      }
   }

   echo color_text("Let's start the game!" . PHP_EOL, 'blue');

   $random_number = rand(1, 100);
   $guesses = [];
   $showClues = ceil($chances / 2);
   $hintGiven = false;

   // Start the timer
   $startTime = microtime(true);

   while (!in_array($random_number, $guesses) && $chances > 0) {
      echo PHP_EOL;
      $guess = (int) get_user_input("Enter your guess: ");
      if (!is_numeric($guess) || $guess < 1 || $guess > 100) {
         echo color_text("Please enter a valid number between 1 and 100." . PHP_EOL, 'red');
         continue;
      }

      $guesses[] = $guess;
      $chances--;

      if ($guess < $random_number) {
         echo color_text("Incorrect! The number is greater than $guess." . PHP_EOL, 'yellow');
      } elseif ($guess > $random_number) {
         echo color_text("Incorrect! The number is less than $guess." . PHP_EOL, 'yellow');
      } else {
         // Stop the timer
         $endTime = microtime(true);
         $timeTaken = $endTime - $startTime;

         echo color_text("Congratulations! You guessed the correct number in " . count($guesses) . " attempts." . PHP_EOL, 'green');
         echo color_text("It took you " . round($timeTaken, 2) . " seconds." . PHP_EOL, 'cyan');

         // Update high score if applicable
         if (count($guesses) < $highScores[$difficultyKey]['attempts'] ||
            (count($guesses) == $highScores[$difficultyKey]['attempts'] && $timeTaken < $highScores[$difficultyKey]['time'])) {
            $highScores[$difficultyKey] = ['attempts' => count($guesses), 'time' => $timeTaken];
            echo color_text("New high score for $difficultyKey difficulty!" . PHP_EOL, 'green');
         }
         break;
      }

      if (count($guesses) == $showClues && !$hintGiven) {
         $hintGiven = true;
         if ($random_number % 2 == 0) {
            echo color_text("Hint: The number is even." . PHP_EOL, 'cyan');
         } else {
            echo color_text("Hint: The number is odd." . PHP_EOL, 'cyan');
         }
      }

      echo color_text("Remaining chances: $chances" . PHP_EOL, 'blue');
   }

   if ($chances == 0 && !in_array($random_number, $guesses)) {
      echo PHP_EOL;
      echo color_text("Sorry! You've used all your chances. The correct number was $random_number." . PHP_EOL, 'red');
   }

   echo PHP_EOL;
   echo "Your guesses were: " . implode(", ", $guesses) . PHP_EOL;

   // Display high scores
   echo PHP_EOL . color_text("High Scores:", 'yellow') . PHP_EOL;
   foreach ($highScores as $level => $score) {
      if ($score['attempts'] === PHP_INT_MAX && $score['time'] === PHP_INT_MAX) {
         echo color_text(ucfirst($level) . ": N/A" . PHP_EOL, 'cyan');
      } else {
         echo color_text(ucfirst($level) . ": " . $score['attempts'] . " attempts, " . round($score['time'], 2) . " seconds" . PHP_EOL, 'cyan');
      }
   }

   $playAgain = prompt_choice("Do you want to play again? ");
   if (strtolower($playAgain) == 'n') {
      echo color_text("Exiting the game. Thanks for playing!" . PHP_EOL, 'blue');
      $keepPlaying = false;
   }
}
