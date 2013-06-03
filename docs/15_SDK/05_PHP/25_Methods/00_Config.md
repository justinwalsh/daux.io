Returns the config object that is assosicated with the current API key.

To retreive the config simply use the $cms variable:

    $config = $cms->config();

You can also filter your config to a specific collection. 

    $team_config = $cms->config("team");