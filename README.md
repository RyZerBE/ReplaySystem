# ReplayRecorder
****

**The ReplaySystem was created for RyZerBE.**

**High-performance recording and playing of worlds**

**Thanks to Matze**

Imports
```
use matze\replaysystem\recorder\replay\Replay;
use matze\replaysystem\recorder\replay\ReplayManager;
```

Start replay:

```
$replay = new Replay($level);
$replay->startRecording();
$replay->setSpawn($spawn);
```

Stop and save replay:

```
$replay = ReplayManager::getInstance()->getReplayByLevel($level);
if(is_null($replay)) return;
$replay->stopRecording();
```

Pause recording:

```
$replay = ReplayManager::getInstance()->getReplayByLevel($level);
if(is_null($replay)) return;
$replay->setRunning(bool $running);
```