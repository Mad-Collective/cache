<?php
namespace spec\Cmp\Cache\Backend;

use Cmp\Cache\Backend\TaggedCache;
use Cmp\Cache\Cache;
use Cmp\Cache\Exceptions\NotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class TaggedCacheSpec
 *
 * @package spec\Cmp\Cache\Backend
 * @mixin TaggedCache
 */
class TaggedCacheSpec extends ObjectBehavior
{
    private $uid = 'dummy_uid';

    private $tagKey = 'tag:dummy';
    
    function let(Cache $store)
    {
        $this->beConstructedWith($store, 'dummy');
        $store->get($this->tagKey)->willReturn($this->uid);
        $store->set($this->tagKey, $this->uid)->willReturn(true);
    }

    function it_generates_namespace_key_correctly(Cache $store)
    {
        $store
            ->get($this->tagKey)
            ->willReturn()
            ->shouldBeCalled();

        $store
            ->set($this->tagKey, Argument::type('string'))
            ->willReturn(true)
            ->shouldBeCalled();

        $store
            ->get(Argument::containingString('sux'), null)
            ->willReturn(false)
            ->shouldBeCalled();

        $this->get('sux')->shouldBe(false);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Cmp\Cache\Backend\TaggedCache');
        $this->shouldThrow('Cmp\Cache\Cache');
    }

    function it_can_store_items(Cache $store)
    {
        $store
            ->set("$this->uid:key", 'value', null)
            ->willReturn(true)
            ->shouldBeCalled();
        $this->set('key', 'value')->shouldBe(true);
    }

    function it_can_store_multiple_items(Cache $store)
    {
        $items = ['a' => 1, 'b' => 2];
        $storedItems = ["$this->uid:a" => 1, "$this->uid:b" => 2];
        $store
            ->setItems($storedItems, null)
            ->willReturn(true)
            ->shouldBeCalled();
        $this->setItems($items)->shouldBe(true);
    }

    function it_deletes_an_item(Cache $store)
    {
        $store
            ->delete("$this->uid:key")
            ->willReturn(true)
            ->shouldBeCalled();

        $this->delete('key')->shouldBe(true);
    }

    function it_deletes_multiple_items(Cache $store)
    {
        $store
            ->deleteItems(["$this->uid:a","$this->uid:b"])
            ->willReturn(true)
            ->shouldBeCalled();

        $this->deleteItems(['a','b'])->shouldBe(true);
    }

    function it_checks_item_existence(Cache $store)
    {
        $store
            ->has("$this->uid:a")
            ->willReturn(true)
            ->shouldBeCalled();

        $store->has("$this->uid:b")
            ->willReturn(false)
            ->shouldBeCalled();

        $this->has('a')->shouldBe(true);
        $this->has('b')->shouldBe(false);
    }

    function it_gets_an_item(Cache $store)
    {
        $store
            ->get("$this->uid:a", null)
            ->willReturn('heyo!')
            ->shouldBeCalled();

        $this->get('a')->shouldBe('heyo!');
    }

    function it_gets_default_for_missing_item(Cache $store)
    {
        $store
            ->get("$this->uid:a", false)
            ->willReturn(false)
            ->shouldBeCalled();

        $this->get('a', false)->shouldBe(false);
    }

    function it_gets_multiple_items(Cache $store)
    {
        $store
            ->getItems(["$this->uid:a", "$this->uid:b"])
            ->willReturn(["$this->uid:a" => 'hey', "$this->uid:b" => 'you'])
            ->shouldBeCalled();

        $this->getItems(['a','b'])->shouldBe(['a' => 'hey', 'b' => 'you']);
    }

    function it_demands_an_item(Cache $store)
    {
        $store
            ->demand("$this->uid:a")
            ->willReturn('value')
            ->shouldBeCalled();

        $this->demand('a')->shouldBe('value');
    }

    function it_throws_notfoundexception_on_demand_fail(Cache $store)
    {
        $store
            ->demand("$this->uid:a")
            ->willThrow(NotFoundException::class)
            ->shouldBeCalled();

        $this->shouldThrow(new NotFoundException('a'))->duringDemand('a');
    }


    function it_can_get_the_remaining_time_to_live(Cache $store)
    {
        $store
            ->getTimeToLive("$this->uid:a")
            ->willReturn(123)
            ->shouldBeCalled();

        $this->getTimeToLive('a')->shouldBe(123);
    }

    function it_flushes_namespace_only(Cache $store)
    {
        $store
            ->delete($this->tagKey)
            ->willReturn(true)
            ->shouldBeCalled();
        $store
            ->deleteByPrefix($this->uid)
            ->shouldBeCalled();

        $store->flush()->shouldNotBeCalled();

        $this->flush()->shouldBe(true);
    }

    function it_can_append_elements_to_a_key(Cache $store)
    {
        $key = 'probeNonExistingList';
        $value = 'probe';

        $store->appendList("$this->uid:$key", $value)->willReturn(1)->shouldBeCalled();
        $this->appendList($key, $value)->shouldBe(true);
    }

    function it_can_increment_value_of_a_key(Cache $store)
    {
        $key = 'foo';
        $store->increment("$this->uid:$key")->willreturn(1)->shouldBeCalled();
        $this->increment($key)->shouldBe(true);
    }
}
