<?php

/**
 *
 * Copyright (c) 2022 cooldogedev
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @auto-license
 */

declare(strict_types=1);

namespace cooldogedev\TNTTag;

use cooldogedev\TNTTag\constant\ItemConstants;
use cooldogedev\TNTTag\game\handler\MatchHandler;
use cooldogedev\TNTTag\game\handler\PreStartHandler;
use cooldogedev\TNTTag\session\Session;
use cooldogedev\TNTTag\utility\form\NormalForm;
use cooldogedev\TNTTag\utility\message\KnownMessages;
use cooldogedev\TNTTag\utility\message\LanguageManager;
use cooldogedev\TNTTag\utility\message\TranslationKeys;
use Exception;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class EventListener implements Listener
{
    protected TNTTag $plugin;

    public function __construct(TNTTag $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @priority MONITOR
     * @ignoreCanceled true
     */
    public function onEntityDamage(EntityDamageEvent $event): void
    {
        if ($event->getCause() === EntityDamageEvent::CAUSE_ENTITY_ATTACK) {
            return;
        }

        $player = $event->getEntity();

        if (!$player instanceof Player) {
            return;
        }

        $session = $this->getPlugin()->getSessionManager()->getSession($player->getUniqueId()->getBytes());

        if (!$session->inGame()) {
            return;
        }

        $game = $session->getGame();
        $session = $game->getPlayerManager()->getSession($player->getUniqueId()->getBytes());

        if ($event->getCause() === EntityDamageEvent::CAUSE_BLOCK_EXPLOSION) {
            $game->explodePlayer($session, true);
        }

        $event->setBaseDamage(0);
        $event->cancel();
    }

    public function getPlugin(): TNTTag
    {
        return $this->plugin;
    }

    /**
     * @priority MONITOR
     */
    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event): void
    {
        $player = $event->getEntity();
        $attacker = $event->getDamager();

        if (!$player instanceof Player || !$attacker instanceof Player) {
            return;
        }

        $session = $this->getPlugin()->getSessionManager()->getSession($player->getUniqueId()->getBytes());
        $attackerSession = $this->getPlugin()->getSessionManager()->getSession($attacker->getUniqueId()->getBytes());

        $game = $session->getGame();

        if (!$session->inGame() || !$attackerSession->inGame()) {
            return;
        }

        if (!$game->getHandler() instanceof MatchHandler || !$attackerSession->isTagged() || $session->isTagged()) {
            $event->cancel();
            return;
        }

        $session->setTagged(true);
        $attackerSession->setTagged(false);

        $game->broadcastMessage(LanguageManager::getMessage(KnownMessages::TOPIC_TAGGED, KnownMessages::TAGGED_TAGGED), [
            TranslationKeys::PLAYER => $player->getDisplayName()
        ]);

        $event->setBaseDamage(0);
    }

    /**
     * @ignoreCanceled true
     */
    public function onPlayerExhaust(PlayerExhaustEvent $event): void
    {
        $player = $event->getPlayer();
        $session = $this->getPlugin()->getSessionManager()->getSession($player->getUniqueId()->getBytes());
        if (!$session->inGame()) {
            return;
        }
        $event->cancel();
    }

    /**
     * @ignoreCanceled true
     */
    public function onPlayerItemHeld(PlayerItemHeldEvent $event): void
    {
        $player = $event->getPlayer();
        $session = $this->getPlugin()->getSessionManager()->getSession($player->getUniqueId()->getBytes());

        try {
            $actionType = $event->getItem()->getNamedTag()->getInt(ItemConstants::ITEM_TYPE_IDENTIFIER);
        } catch (Exception) {
            return;
        }

        if (!$session->inGame()) {
            return;
        }

        $game = $session->getGame();

        switch ($actionType) {
            case ItemConstants::ITEM_TELEPORTER:
                $form = new NormalForm("Teleporter", "Select a player:");
                $callback = function (Player $player, ?string $data) use ($game): void {
                    if (!$data) {
                        return;
                    }
                    $target = $this->getPlugin()->getServer()->getPlayerByPrefix($data);
                    $targetSession = $target ? $game->getPlayerManager()->getSession($target->getUniqueId()->getBytes()) : null;
                    if (!$targetSession || $targetSession->getState() !== Session::PLAYER_STATE_ALIVE) {
                        $player->sendMessage(TextFormat::RED . "That player is no longer in the game.");
                        return;
                    }
                    $player->teleport($target->getPosition());
                };
                $form->setCallback($callback);
                foreach ($game->getPlayerManager()->getSessions() as $session) {
                    $form->addButton($session->getPlayer()->getDisplayName());
                }
                $player->sendForm($form);
                break;
            case ItemConstants::ITEM_PLAY_AGAIN:
                $game->getPlayerManager()->removeFromGame($session, true);
                $this->getPlugin()->getGameManager()->queueToGame([$session], null, true);
                break;
            case ItemConstants::ITEM_BACK_TO_LOBBY:
                $game->getPlayerManager()->removeFromGame($session, true);
        }
    }

    /**
     * @ignoreCanceled true
     */
    public function onPlayerItemUse(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $session = $this->getPlugin()->getSessionManager()->getSession($player->getUniqueId()->getBytes());

        if (!$session->inGame() || !$session->getGame()->getHandler() instanceof PreStartHandler) {
            return;
        }

        try {
            $actionType = $event->getItem()->getNamedTag()->getInt(ItemConstants::ITEM_TYPE_IDENTIFIER);
        } catch (Exception) {
            return;
        }

        if ($actionType !== ItemConstants::ITEM_QUIT_GAME) {
            return;
        }

        $session->getGame()->getPlayerManager()->removeFromGame($session, true);
    }

    /**
     * @ignoreCanceled true
     */
    public function onPlayerDropItem(PlayerDropItemEvent $event): void
    {
        $player = $event->getPlayer();
        $session = $this->getPlugin()->getSessionManager()->getSession($player->getUniqueId()->getBytes());
        if ($session->inGame()) {
            $event->cancel();
        }
    }

    /**
     * @ignoreCanceled true
     * @priority MONITOR
     */
    public function onPlayerChat(PlayerChatEvent $event): void
    {
        $player = $event->getPlayer();
        $session = $this->getPlugin()->getSessionManager()->getSession($player->getUniqueId()->getBytes());
        $game = $session->getGame();

        if ($game === null) {
            return;
        }

        $event->setFormat(
            $session->getState() === Session::PLAYER_STATE_ALIVE ? LanguageManager::translate(LanguageManager::getMessage(KnownMessages::TOPIC_CHAT, KnownMessages::CHAT_ALIVE), [
                TranslationKeys::PLAYER => $player->getDisplayName(),
                TranslationKeys::MESSAGE => $event->getMessage(),
                TranslationKeys::GOAL => $session->getGoal()])
                :
                LanguageManager::translate(LanguageManager::getMessage(KnownMessages::TOPIC_CHAT, KnownMessages::CHAT_SPECTATOR), [
                    TranslationKeys::PLAYER => $player->getDisplayName(),
                    TranslationKeys::MESSAGE => $event->getMessage()])
        );

        $recipients = $session->getState() === Session::PLAYER_STATE_ALIVE ? $game->getPlayerManager()->getSessions(null) : $game->getPlayerManager()->getSessions(Session::PLAYER_STATE_SPECTATOR);

        $event->setRecipients(array_map(fn(Session $session) => $session->getPlayer(), $recipients));
    }

    /**
     * @ignoreCanceled true
     */
    public function onInventoryTransaction(InventoryTransactionEvent $event): void
    {
        $player = $event->getTransaction()->getSource();
        $session = $this->getPlugin()->getSessionManager()->getSession($player->getUniqueId()->getBytes());
        if ($session->inGame()) {
            $event->cancel();
        }
    }

    /**
     * @ignoreCanceled true
     */
    public function onPlayerLogin(PlayerLoginEvent $event): void
    {
        $player = $event->getPlayer();
        $this->getPlugin()->getSessionManager()->createSession($player);
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $session = $this->getPlugin()->getSessionManager()->getSession($player->getUniqueId()->getBytes());
        if ($this->getPlugin()->getConfig()->get("behaviour")["queue-on-login"] && $session) {
            $this->getPlugin()->getGameManager()->queueToGame([$session], null, true, true);
        }
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        $session = $this->getPlugin()->getSessionManager()->getSession($player->getUniqueId()->getBytes());
        $session->save();

        $game = $session->getGame();
        $game?->getPlayerManager()->removeFromGame($session, true);

        $this->getPlugin()->getSessionManager()->removeSession($player->getUniqueId()->getBytes());
    }
}
