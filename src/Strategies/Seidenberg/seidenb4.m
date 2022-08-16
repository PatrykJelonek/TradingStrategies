%seidenb wg Seidenberg s.123(Joe Krutsinger) A.Wilinski (C)2018

%v1 koncepcja
%v2 optymalizacja r
%v3 - dodanie SL; warunek na otwarcie wieksze niz stop
%v4 po doborze parametrów i rezygnacji z SL (nie mozna jednoznacznie
%usteliæ miejsca min lub max - przed czy po otwarciu

clear all

FW20WS191018; %[date OHLC Vol LOP]

m=size(C);
r=0.65;%68;
ld=0; %liczba dlugich
ls=0;
rec=-111111;
spread=1;
SL=16;


ld=0; %liczba dlugich
ls=0;
ll=0;
lo=0;  %liczba otwarc powyzej wczorajszego stop
sl=0;
for i=49:m(1)-1
    ll=ll+1;
    zl(i)=0;
    zs(i)=0;
    range(i)=C(i-1,3)-C(i-1,4);
    stop1(i)=C(i-1,3)+r*range(i);
    
    if C(i,2)<stop1(i) && C(i,3)>stop1(i) %&& C(i-2,6)>C(i-1,6)
        ld=ld+1;
        zl(i)=C(i,5)-stop1(i)-spread;
        
    end
    if C(i,2)>stop1(i)
        lo=lo+1;
        zl(i)=C(i,5)-C(i,2)-spread;
   
        if C(i,2)-C(i,4)>SL
        sl=sl+1;
        zl(i)=-SL-spread;
        end
    end
    
    stop2(i)=C(i-1,4)-r*range(i);
    if C(i,4)<stop2(i) && C(i,2)>stop2(i)
        ls=ls+1;
        zs(i)=stop2(i)-C(i,5)-spread;
        
    end
    if C(i,2)<stop2(i)
        lo=lo+1;
        zs(i)=C(i,2)-C(i,5)-spread;
   
          if C(i,3)-C(i,2)>SL
             sl=sl+1;
             zs(i)=-SL-spread;
          end
    end
    


zsl=cumsum(zl);
zss=cumsum(zs);
zcum=zsl+zss;

if zcum(end)>rec
    rec=zcum(end);
    paropt=[r ll ld ls lo sl];
    zr=zcum;
    zlr=zsl;
    zsr=zss;
    lor=lo;
    
end

end

%obl CR dl rekordowego wyniku

mdd=0;
mm=size(zr);

for i=1:mm(2)
    obni(i)=0;
    mloc(i)=max(zr(1:i));
    if zr(i)<mloc(i)
        obni(i)=mloc(i)-zr(i);
    end
end

mdd=max(obni);
calmar=zr(end)/mdd


paropt
rec

figure(1)
plot(zlr,'-g')
hold on
plot(zsr,'-r')
hold on
plot(zr)

