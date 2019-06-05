<?php
namespace App\VkontakteBot\BotKeyboard;

use Illuminate\Support\Facades\Log;

class ButtonRow
{

    private $buttonRow = [];

    public function addButton(array $button): self
    {
        $this->buttonRow[] = $button;

        return $this;
    }

    public function addButtons(array $button, int $getCount): self
    {
        for ($index=0; $index<$getCount; $getCount++){
            $this->buttonRow[] = $button[$index];

            return $this->buttonRow;
        }

    }




    public function getRow(): array
    {
        return $this->buttonRow;
    }
}