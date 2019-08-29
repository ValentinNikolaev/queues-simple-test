## Simple demo for enqueue and symfony/messenger redis queues
- logs via Monolog
- logs in console
- every type of queues has stats command to see what and how happened
- to increase performance just add new consumer via running new process
- supoorts signual interruption

## Enqueue
Consumers:  ``php run.php consumer:enqueue``

Producer:  ``php run.php producer:enqueue``

Stats: ``php run.php stats:enqueue``

Just get and process message system. One message per subscriber with acknowledge.

## Enquque Stats 
Enqueue info

dsn:redis+phpredis://localhost:6379

queue: aQueue

redis queue type: list

redis queue len: 101

Queue delayed

queue: aQueue:delayed

queue len: 

Queue reserved

queue: aQueue:reserved

queue le

## Messenger (require REDIS > 5.0 version)

https://redis.io/topics/streams-intro

Consumers:  ``php run.php consumer:messenger``

Producer:  ``php run.php producer:messenger``

Stats: ``php run.php stats:messenger``

## Messenger , example. We have controls on workers, groups

dsn:redis://localhost/mQueue/mGroup

queue: mQueue

queue type: stream

queue len: 210

Groups: 

| name   | consumers | pending messages (delivered but not yet acknowledged) | last-delivered-id |
|--------|-----------|-------------------------------------------------------|-------------------|
| mGroup | 165       | 210                                                   | 1567078340665-0   |

Consumers for group mGroup: 

| name                          | pending | iddle    |
|-------------------------------|---------|----------|
| Abbigail_Lehner               | 1       | 1.688809 |
| Abdul_Jenkins                 | 1       | 1.688761 |
| Aidan_Quigley                 | 1       | 1.688661 |
| Aileen_Huel                   | 1       | 1.688623 |
| Alford_Grady                  | 1       | 1.688631 |
| Allan_Moore                   | 1       | 1.688691 |
| Amber_Schinner                | 2       | 1.299377 |
| America_Corwin                | 2       | 1.422411 |
| Annabell_Strosin              | 2       | 1.233291 |
| Annabelle_Feest               | 1       | 1.688774 |
| Antonietta_Torphy             | 1       | 1.68882  |
| Autumn_Goodwin                | 1       | 1.688802 |
| Avery_Hilpert                 | 1       | 1.688704 |
| Brant_Balistreri              | 1       | 1.688779 |
| Brendon_Hessel                | 1       | 1.688815 |
| Carroll_Hickle_V              | 1       | 1.646033 |
| Cedrick_Jacobs                | 1       | 1.688783 |
