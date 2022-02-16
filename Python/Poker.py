#------------------------------------------------------#
#             PYTHON POKER: 5 CARD DRAW                #
#    Code by John Wenzel - johngwenzel@gmail.com       #
#                     2/10/22                          #
#               Modify as you please                   #
#------------------------------------------------------#
import random
import sys

class Game:
    def __init__(self,num_players=0,players=False,game_name=False):
        self.game_name = game_name
        self.mode = 'PLAY'
        self.bank = 1000
        
        while self.mode == 'PLAY':
            self.deck = Deck()
            self.hand = Hand()
            #self.load_hand("3 OF A KIND")

            self.deck.shuffle_deck(5)
            num_cards = 5
            self.deal(num_cards)

            str_bet = input("Place Bet from $1 to $50: ")
            bet = int(str_bet)
            
            self.print_cards()
            need_input = True
            while need_input == True:
                user_input = input("Discard up to 3 cards or (K)eep all: ")
                if user_input == 'K':
                    break
                else:
                    discards_list = user_input.split()
                    try:
                        map_discards = map(int, discards_list)
                        discards = list(map_discards)
                        bad_input = False
                        for d in discards:
                            if d not in range(1,num_cards + 1):
                                bad_input = True
                    except:
                        bad_input = True
                    if bad_input:
                        print("\n\nBad Input. Numbers are out of range. Try Again.\n\n")
                        need_input = True
                    else:
                        need_input = False
            if user_input != 'K':     
                self.hand.discard(discards)
                self.deal(len(discards))
            self.print_cards()
            self.hand.tally_hand()
            self.hand.print_tally()
            self.hand.set_hand_rank()
            won = self.hand.win(bet)
            self.bank = self.bank + won - bet
            print("You got a " + self.hand.rank)
            print("You bet $" + str(bet))
            if won > 0:
                print("You won $" + str(won))
            print("Bank Balance: $" + str(self.bank))
            
            entry = input("OPTIONS: (P)lay Again. (Q)uit: ")
            if entry.upper() == 'Q':
                self.mode = 'QUIT'
            else:
                self.mode = 'PLAY'

        print("Number of Cards in Deck: " + str(len(self.deck.cards)))
        if self.mode == 'QUIT':
            print("Goodbye!")
            sys.exit()

    def print_cards(self):
        self.output = [] * 10
        for i in range(len(self.hand.cards)):
            self.hand.cards[i].set_footer_label(i + 1)
            self.hand.cards[i].build_card()
            self.add_to_output(self.hand.cards[i].output)
        for i in range(len(self.output)):
            print(self.output[i])
        
    def deal(self,num_cards=5):
        for c in range(num_cards):
            self.hand.add_to_hand(self.deck.cards.pop(),c + 1)

    def load_hand(self, hand="ROYAL FLUSH"):
        #to validate logic is working
        #use unshuffled deck
        if hand == "ROYAL FLUSH":
            #0,9,10,11,12
            self.hand.add_to_hand(self.deck.cards[0],1)
            self.hand.add_to_hand(self.deck.cards[9],2)
            self.hand.add_to_hand(self.deck.cards[10],3)
            self.hand.add_to_hand(self.deck.cards[11],4)
            self.hand.add_to_hand(self.deck.cards[12],5)
        elif hand == "STRAIGHT FLUSH":
            self.hand.add_to_hand(self.deck.cards[3],1)
            self.hand.add_to_hand(self.deck.cards[4],2)
            self.hand.add_to_hand(self.deck.cards[5],3)
            self.hand.add_to_hand(self.deck.cards[6],4)
            self.hand.add_to_hand(self.deck.cards[7],5)
        elif hand == "FLUSH":
            self.hand.add_to_hand(self.deck.cards[0],1)
            self.hand.add_to_hand(self.deck.cards[1],2)
            self.hand.add_to_hand(self.deck.cards[2],3)
            self.hand.add_to_hand(self.deck.cards[3],4)
            self.hand.add_to_hand(self.deck.cards[8],5)
        elif hand == "STRAIGHT":
            self.hand.add_to_hand(self.deck.cards[3],1)
            self.hand.add_to_hand(self.deck.cards[4],2)
            self.hand.add_to_hand(self.deck.cards[5],3)
            self.hand.add_to_hand(self.deck.cards[6],4)
            self.hand.add_to_hand(self.deck.cards[20],5) #+14 for new suit and next card
        elif hand == "4 OF A KIND":
            self.hand.add_to_hand(self.deck.cards[0],1)
            self.hand.add_to_hand(self.deck.cards[10],2)
            self.hand.add_to_hand(self.deck.cards[23],3)
            self.hand.add_to_hand(self.deck.cards[36],4)
            self.hand.add_to_hand(self.deck.cards[49],5)            
        elif hand == "3 OF A KIND":
            self.hand.add_to_hand(self.deck.cards[0],1)
            self.hand.add_to_hand(self.deck.cards[10],2)
            self.hand.add_to_hand(self.deck.cards[23],3)
            self.hand.add_to_hand(self.deck.cards[36],4)
            self.hand.add_to_hand(self.deck.cards[40],5) 
        else:
            return False
        
        self.hand.order_cards()
        return True
            
            

    def add_to_output(self, lines):
        i = 0
        while i < len(lines):
            if i >= len(self.output):
                self.output.append("") #initialize
            self.output[i] = self.output[i] + str(lines[i])
            i += 1
            
class Deck:
    
    def __init__(self):
        suits = {'H':'Hearts','D':'Diamonds','C':'Clubs','S':'Spades'}
        cardsuits = ['H' for i in range(13)] + \
            ['D' for i in range(13,26)] + \
            ['C' for i in range(26,39)] + \
            ['S' for i in range(40,53)]

        cards = ['A',2,3,4,5,6,7,8,9,10,'J','Q','K'] * 4
        j = 0;
        self.cards = []
        while j < len(cards):
            self.cards.append(Card(cards[j],cardsuits[j],self.cardvalue(j)))
            j += 1
    
        
    def print_cards(self):
        j = 0;
        while j < len(self.cards):
            self.cards[j].print_card("long")
            j += 1

    def cardvalue(self,index):
        values = [1,2,3,4,5,6,7,8,9,10,10,10,10] * 4
        return values[index]

    def shuffle_deck(self,num):
        if num < 1 or num > 10:
            num = 7
        for i in range(num):
            random.shuffle(self.cards)

class Card:
    def __init__(self, name, suit, value, footer_label=False):
        lsuits = {'H':'Hearts','D':'Diamonds','C':'Clubs','S':'Spades'}
        numsuits = {'H':1,'D':2,'C':3,'S':4}
        self.name = name # 2,3,4,...Q,K,A
        self.suit = suit
        self.numsuit = numsuits[suit]   # 1,2,3,4 = ♥♦♣♠
        self.vsuit = '♥♦♣♠'[self.numsuit-1]
        self.lsuit = lsuits[suit]
        self.value = value
        self.footer_label = False
        self.output = []
        self.build_card()

        
    def print_card(self):
        for i in range(len(self.output)):
            print(self.output[i])

    def build_card(self):
        self.output = ["┌───────┐", \
                    "| {:<2}    |".format(self.name), \
                    "|       |", \
                    "|   {}   |".format(self.vsuit), \
                    "|       |", \
                    "|    {:>2} |".format(self.name), \
                    "└───────┘"]
        if self.footer_label:
            self.output.append("    {:<2}   ".format(self.footer_label))

    def set_footer_label(self, footer_label):
        self.footer_label = footer_label

class Hand:
    def __init__(self):
        self.cards = []
        self.order = [2,3,4,5,6,7,8,9,10,'J','Q','K','A']
        self.card_tally = []
        self.suit_tally = []
        self.init_tally()
        self.rank = 'UNKNOWN'

    def init_tally(self):
        self.card_tally = [[2,0],[3,0],[4,0],[5,0],[6,0],[7,0],[8,0], \
                          [9,0],[10,0],['J',0],['Q',0],['K',0],['A',0]]
        self.suit_tally = [['H',0],['D',0],['C',0],['S',0]]

    def set_hand(self, cards):
        self.cards = cards

    def add_to_hand(self,card,footer_label=False):
        card.set_footer_label(footer_label)
        card.build_card()
        self.cards.append(card)
        return True       
    
    def discard(self,index_list):
        discard_cards = []
        for i in index_list:
            discard_cards.append(self.cards[int(i)-1])
        for card in discard_cards:
            self.cards.remove(card)
            
    def hand_size():
        return self.cards.count()

    def has_hand(self):
        if self.hand_size() == 0:
            return False
        return True

    def win(self, bet=1):
        winnings = 0
        multiplier = [["ROYAL FLUSH",500],
                      ["STRAIGHT FLUSH",200],
                      ["4 OF A KIND",100],
                      ["FULL HOUSE",50],
                      ["FLUSH",30],
                      ["STRAIGHT",20],
                      ["3 OF A KIND",15],
                      ["2 PAIR",10],
                      ["1 PAIR",1]]
        for m in multiplier:
            if self.rank.upper() == m[0]:
                winnings = bet * m[1]
        return winnings

    def set_hand_rank(self):
        #HAND RANKINGS
        #1 Royal Flush
        #2 Straight Flush
        #3 4 of a Kind
        #4 Full House
        #5 Flush
        #6 Straight
        #7 3 of a Kind
        #8 2 Pair
        #9 1 Pair
        #10 High Card

        #check for 4 of a kind
        #   if so, done
        #check for 3 of a kind
        #   if so, check for fullhouse
        #       if fullhouse, done
        #   else skip pair check
        #check for pair
        #   if so, check for 2 pair
        #   if either done
        #check for straight
        #check for flush
        #check for straight flush
        #check for royal flush

        if self.has_four_of_a_kind():
            self.rank = "4 of a Kind"
        elif self.has_three_of_a_kind():
            if self.has_full_house():
                self.rank = "Full House"
            else:
                self.rank = "3 of a Kind"
        elif self.has_one_pair():
            self.rank = "1 Pair"
        elif self.has_two_pair():
            self.rank = "2 Pair"
        else:
            straight = self.has_straight()
            flush = self.has_flush()
            sflush = self.has_straight_flush(flush, straight)
            if sflush:
                if self.has_royal_flush(flush, straight):
                    self.rank = "Royal Flush"
                else:
                    self.rank = "Straight Flush"
            else:
                if straight:
                    self.rank = "Straight"
                elif flush:
                    self.rank = "Flush"
        if self.rank == "UNKNOWN":
            self.rank = "High Card"

    def order_cards(self):
        #lowest to highest
        newhand = []
        for o in self.order:
            for card in self.cards:
                if str(card.name) == str(o):
                    newhand.append(card)
        self.set_hand(newhand)

    def tally_hand(self):
        self.init_tally()
        for card in self.cards:
            for i in range(len(self.card_tally)):
                if str(card.name) == str(self.card_tally[i][0]):
                    self.card_tally[i][1] += 1
            for j in range(len(self.suit_tally)):
                if card.suit == self.suit_tally[j][0]:
                    self.suit_tally[j][1] += 1

    def print_tally(self):
        print("RANKS")
        for i in range(len(self.card_tally)):
            print(str(self.card_tally[i][0]) + " -> " + str(self.card_tally[i][1]))
        print("SUITS")
        for j in range(len(self.suit_tally)):
            print(str(self.suit_tally[j][0]) + " -> " + str(self.suit_tally[j][1]))
                
    def has_flush(self):
        suit = self.cards[0].suit
        result = True
        for i in range(1,5):
            if suit != self.cards[i].suit:
                result = False
        return result

    def has_straight(self):
        #10 is the highest the first card can be. 10,J,Q,K,A
        #In self.order, 10 has the index of 8
        for i in range(9):
            if self.cards[0].name == self.order[i]:
                #we've found the start
                #TODO: compare as strings?
                if self.cards[1].name == self.order[i+1] and \
                   self.cards[2].name == self.order[i+2] and \
                   self.cards[3].name == self.order[i+3] and \
                   self.cards[4].name == self.order[i+4]:
                    return True
        return False

    def has_straight_flush(self, is_flush, is_straight):
            return is_flush and is_straight

    def has_royal_flush(self, is_flush, is_straight):
        return is_flush and is_straight and str(self.cards[0].name) == str(10)
            
    def has_one_pair(self):
        num_pairs = 0
        for t in self.card_tally:
            if t[1] == 2:
                num_pairs += 1
        if num_pairs == 1:
            return True
        return False

    def has_two_pair(self):
        num_pairs = 0
        for t in self.card_tally:
            if t[1] == 2:
                num_pairs += 1
        if num_pairs == 2:
            return True
        return False


    def has_three_of_a_kind(self):
        for t in self.card_tally:
            if t[1] == 3:
                return True
        return False

    def has_full_house(self):
        return self.has_three_of_a_kind() and self.has_one_pair()
    
    def has_four_of_a_kind(self):
        for t in self.card_tally:
            if t[1] == 4:
                return True
    
    def high_card(self):
        #this returns highest card, not boolean
        for i in reversed(self.card_tally):
            if self.card_tally[i][1] > 0:
                return self.cards[i]
        return False #this shouldn't happen

    
                       
game = Game()
            
