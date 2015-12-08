## Composer Dependency Auto Update

Automagicly update specified dependencies when their master repositories update.

#### Installing
1. Deploy somewhere. Probably Heroku
 
  [![Deploy to Heroku](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy)
1. Go into your dependency repo on github and add a custom webhook with your new app url. something like **https://thing.herokuapp.com/hook**
1. Set your secret key the same as the key you used in the deployment
1. Add as many repositories as you want by adding other environment variables like **GITHUB_REPO2**, **GITHUB_REPO3**, etc
1. Make sure you include your username and password like **https://user:pass@github.com/org/repo.git**
