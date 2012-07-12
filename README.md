# mod_random_image

## What is it?
It's a rework of the standard Joomla! Random Image module.

## Why is it?
I'm experimenting with new approach to writing Joomla modules. Also, I'm fixing it.

### Fixing it?
If you want CSS to completely control your image sizing, you're pretty much out of luck with the regular one. It insists on putting its own height and width attributes on the inmage tag. You have to write a template override to avoid it.

Also, it's pretty much hostile to the idea of any sort of responsive design, something which should be fixed as well.

## How does it work?
I've "hijacked" the JModuleHelper class for it, but there's no reason it couldn't eventually be a JModuleClass. The idea behind it can be carried into a future approach to handling modules.

Traditionally, the system executes a code fragment located in a file named for the module (_module.php_) which starts the execution. This file has been the collecting point for lots of procedural code, when a future version could simply instantiate the module object and tell it to create its output.

All module code then becomes encapsulated into its own object, lessening chances for side effects and other symptoms of tight coupling.

A side benefit is it increases the unit testability of the module itself, as the instantiation code for the module object can have everything it needs for further execution injected into it. That means the test can easily preload test data into it, and mock objects for it to reference.

## When will it happen?
Dunno. Let's find out first if I've managed to think this through properly, then we'll worry about when. This is an early "proof-of-concept" pass at it. It'll get fleshed out with comments/suggestions received, and tests (of **course** there will be tests, that's part of the point, here, after all).

### More Breakage?
Not necessarily. The code can easily be set to look for the new way first, then fall back to the old way. Both approaches require minimal amounts of code in the core, so supporting both for a while shouldn't be too much of an issue.

## Who Decides?
You do. Tell me I'm stupid, that it's a completely misguided idea, and it goes away. Give it some thought, break it and let me try to fix it, hammer on it, and maybe we can make it happen.
