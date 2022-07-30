<?php

declare(strict_types=1);

namespace cooldogedev\TNTTag\utility;

use cooldogedev\TNTTag\TNTTag;

final class ConfigurationsValidator
{
    protected const TOKEN_CONFIG_VERSION = "config-version";

    // TODO: Clean this up
    public static function validate(TNTTag $plugin): void
    {
        $config = $plugin->getConfig();

        if ($config->get("config-version") !== $plugin->getDescription()->getVersion()) {
            copy($plugin->getDataFolder() . "config.yml", $plugin->getDataFolder() . "config.yml.old");

            $plugin->saveConfig();
            $plugin->saveResource("config.yml", true);
            $plugin->reloadConfig();

            $plugin->getLogger()->warning("Your config file is outdated. It has been backed up to config.old.yml");
        }

        $languageFile = "languages" . DIRECTORY_SEPARATOR . $config->get("language") . ".yml";

        $tokens = ConfigurationsValidator::getTokens(file_get_contents($plugin->getDataFolder() . $languageFile));

        if (ConfigurationsValidator::getToken($tokens, ConfigurationsValidator::TOKEN_CONFIG_VERSION) !== $plugin->getDescription()->getVersion()) {
            copy($plugin->getDataFolder() . $languageFile, $plugin->getDataFolder() . $languageFile . ".old");
            $plugin->saveResource($languageFile, true);
            $plugin->getLogger()->warning("Your language file is either outdated or corrupted (has no tokens). It has been backed up to " . $languageFile . ".old");
        }
    }

    public static function getTokens(string $content): array
    {
        preg_match_all("/###(.*?)###/", $content, $tokens);

        // Skip the matches and just get the groups.
        $tokens = count($tokens[1]) > 0 ? $tokens[1] : [];

        // Trim the tokens.
        $tokens = array_map(fn($token) => trim($token), $tokens);

        // return the tokens as key value pairs.

        return array_combine(array_map(fn(string $token) => trim(explode(":", $token)[0]), $tokens), array_map(fn(string $token) => trim(explode(":", $token)[1]), $tokens));
    }

    public static function getToken(array $tokens, string $token): ?string
    {
        return $tokens[$token] ?? null;
    }
}
