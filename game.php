<?php

define('MAX_NUMBER', 100);
define('EASY_CHANCES', 10);
define('MEDIUM_CHANCES', 5);
define('HARD_CHANCES', 3);

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
 * Display high scores
 */
function display_high_scores($highScores)
{
   echo PHP_EOL . color_text("High Scores:", 'yellow') . PHP_EOL;
   foreach ($highScores as $level => $score) {
      if ($score['attempts'] === PHP_INT_MAX && $score['time'] === PHP_INT_MAX) {
         echo color_text(ucfirst($level) . ": N/A" . PHP_EOL, 'cyan');
      } else {
         echo color_text(ucfirst($level) . ": " . $score['attempts'] . " attempts, " . round($score['time'], 2) . " seconds" . PHP_EOL, 'cyan');
      }
   }
}

/**
 * Get difficulty level from user
 */
function get_difficulty_level()
{
   while (true) {
      echo PHP_EOL . "Please select the difficulty level:" . PHP_EOL;
      echo color_text("1. Easy (10 chances)", 'green') . PHP_EOL;
      echo color_text("2. Medium (5 chances)", 'yellow') . PHP_EOL;
      echo color_text("3. Hard (3 chances)", 'red') . PHP_EOL;
      echo PHP_EOL;

      $difficulty = (int) get_user_input("Enter your choice: ");
      switch ($difficulty) {
         case 1:
            return ['chances' => EASY_CHANCES, 'key' => 'easy'];
         case 2:
            return ['chances' => MEDIUM_CHANCES, 'key' => 'medium'];
         case 3:
            return ['chances' => HARD_CHANCES, 'key' => 'hard'];
         default:
            echo color_text("Invalid choice. Please select a valid difficulty level." . PHP_EOL, 'red');
      }
   }
}

/**
 * Main game loop
 */
function play_game(&$highScores)
{
   $difficulty = get_difficulty_level();
   $chances = $difficulty['chances'];
   $difficultyKey = $difficulty['key'];

   echo color_text("Let's start the game!" . PHP_EOL, 'blue');
   $random_number = rand(1, MAX_NUMBER);
   $guesses = [];
   $hintGiven = false;
   $showClues = ceil($chances / 2);

   $startTime = microtime(true);

   while (!in_array($random_number, $guesses) && $chances > 0) {
      echo PHP_EOL;
      $guess = (int) get_user_input("Enter your guess: ");
      if ($guess < 1 || $guess > MAX_NUMBER) {
         echo color_text("Please enter a valid number between 1 and " . MAX_NUMBER . "." . PHP_EOL, 'red');
         continue;
      }

      $guesses[] = $guess;
      $chances--;

      if ($guess < $random_number) {
         echo color_text("Incorrect! The number is greater than $guess." . PHP_EOL, 'yellow');
      } elseif ($guess > $random_number) {
         echo color_text("Incorrect! The number is less than $guess." . PHP_EOL, 'yellow');
      } else {
         $endTime = microtime(true);
         $timeTaken = $endTime - $startTime;

         echo color_text("Congratulations! You guessed the correct number in " . count($guesses) . " attempts." . PHP_EOL, 'green');
         echo color_text("It took you " . round($timeTaken, 2) . " seconds." . PHP_EOL, 'cyan');

         if (count($guesses) < $highScores[$difficultyKey]['attempts'] ||
            (count($guesses) == $highScores[$difficultyKey]['attempts'] && $timeTaken < $highScores[$difficultyKey]['time'])) {
            $highScores[$difficultyKey] = ['attempts' => count($guesses), 'time' => $timeTaken];
            echo color_text("New high score for $difficultyKey difficulty!" . PHP_EOL, 'green');
         }
         break;
      }

      if (count($guesses) == $showClues && !$hintGiven) {
         $hintGiven = true;
         echo color_text("Hint: The number is " . ($random_number % 2 == 0 ? "even" : "odd") . "." . PHP_EOL, 'cyan');
      }

      echo color_text("Remaining chances: $chances" . PHP_EOL, 'blue');
   }

   if ($chances == 0 && !in_array($random_number, $guesses)) {
      echo PHP_EOL;
      echo color_text("Sorry! You've used all your chances. The correct number was $random_number." . PHP_EOL, 'red');
   }

   echo PHP_EOL . "Your guesses were: " . implode(", ", $guesses) . PHP_EOL;
   display_high_scores($highScores);
}

// Entry point
echo color_text("========================================", 'blue') . PHP_EOL;
echo color_text("  Welcome to the Number Guessing Game!  ", 'yellow') . PHP_EOL;
echo color_text("========================================", 'blue') . PHP_EOL;
echo color_text("I'm thinking if a number between 1 and " . MAX_NUMBER . "." . PHP_EOL, 'green');
echo color_text("Can you guess it?" . PHP_EOL, 'green');
echo color_text("========================================", 'blue') . PHP_EOL;

$keepPlaying = true;

while ($keepPlaying) {
   play_game($highScores);
   $playAgain = prompt_choice("Do you want to play again? ");
   if (strtolower($playAgain) == 'n') {
      echo color_text("Exiting the game. Thanks for playing!" . PHP_EOL, 'blue');
      $keepPlaying = false;
   }
}
