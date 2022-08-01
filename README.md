# TNTTag
<p align="center">
	<a href="https://github.com/cooldogedev/TNTTag"><img
            src="https://github.com/cooldogedev/TNTTag/blob/main/assets/icon.png?raw=true"/></a><br>
	An extremely customizable TNTTag mini-game designed for scalability and simplicity.
</p>

## Features
- Customisable messages and scoreboard
- Multi arena support
- Waiting lobby support
- Auto-queue support
- Game statistics
- SQLite support
- MySQL support

## Commands
|    Command    |          Description          |         Permission         |
|:-------------:|:-----------------------------:|:--------------------------:|
| tnttag create |      Create a new arena.      | `tnttag.subcommand.create` |
| tnttag delete | Delete an existing arena. | `tnttag.subcommand.delete` |
|  tnttag list  | List all available arenas. |  `tnttag.subcommand.list`  |
|  tnttag join  |    Join a game.    |  `tnttag.subcommand.join`  |
|  tnttag quit  |    Quit a game.    |  `tnttag.subcommand.quit`  |

## Arena creation
### Usage
`tnttag create <name:string> <world:string> <countdown:string> <min_players:string> <max_players:string> <round_duration:string> <grace_duration:string> <end_duration:string> [lobby:string]`
### Example
Example: `tnttag create test game-world 30 8 40 60 20 10 game-lobby`
