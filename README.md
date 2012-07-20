# mod_random_image

## What is it?
It's a rework of the standard Joomla! Random Image module.

## Why is it?
I'm experimenting with new approach to writing Joomla modules. Also, I'm fixing it.

### Fixing it?
If you want CSS to completely control your image sizing, you're pretty much out of luck with the regular one. It insists on putting its own height and width attributes on the inmage tag. You have to write a template override to avoid it.

Also, it's pretty much hostile to the idea of any sort of responsive design, something which should be fixed as well.

## How does it work?
I've not made it a child of any specific class, but there's no reason it couldn't eventually be a child of a JModuleClass. Or, for that matter, no reason it has to be in *any* class tree, so long as it implements a specific interface. The idea behind it can be carried into a future approach to handling modules, and if so that would be the appropriate time to address those issues.

Traditionally, the system executes a code fragment located in a file named for the module (_module.php_) which starts the execution. This file has been the collecting point for lots of procedural code, when a future version could simply instantiate the module object and tell it to create its output.

All module code then becomes encapsulated into its own object, lessening chances for side effects and other symptoms of tight coupling.

A side benefit is this increases the unit testability of the module itself, as the instantiation code for the module object can have everything it needs for further execution injected into it. This injection is the basis for increased testability. It means the test can easily preload test data into it, and mock objects for it to reference. It decreases the dependency of the module code on the actual Joomla code, and anything that breaks dependencies improves testability, and eventually, reliability.

The premise is made clear by looking at the helper.php code: The object is instantiated by passing it the 'params' object. In 'normal' operation, this is a JRegistry object with the parameters for the module in it. But we can use *any* object that behaves like JRegistry. Hence, by passing in a test object, or a mock, we can break any dependency the code has on the rest of the Joomla system, making the tests run faster, and be more isolated from any side effects. We can also use that to set up specific tests with a specific set of parameters, allowing us to much more easily duplicate an error from the field, and an automated acceptance test can feed in a complete range of parameters for full-range testing.

The unfortunate reliance on static calls into JURI, JHTML, JString, and JText means we haven't completely broken the dependencies, but we've localized the untestable code. If/When Joomla itself becomes more testable, we can update the testing here.

## When will it happen?
Dunno. Let's find out first if I've managed to think this through properly, then we'll worry about when. This is an early "proof-of-concept" pass at it. It'll get fleshed out with comments/suggestions received, and tests (of **course** there will be tests, that's part of the point, here, after all).

### More Breakage?
Not necessarily. The code can easily be set to look for the new way first, then fall back to the old way. Both approaches require minimal amounts of code in the core, so supporting both for a while shouldn't be too much of an issue.

## Who Decides?
You do. Tell me I'm stupid, that it's a completely misguided idea, and it goes away. Give it some thought, break it and let me try to fix it, hammer on it, and maybe we can make it happen.
